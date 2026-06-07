<?php

namespace App\Jobs;

use App\Models\Delivery;
use App\Models\DeliveryAttempt;
use App\Models\Event;
use App\Models\Subscription;
use App\Services\SsrfGuard;
use App\Services\WebhookSigner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DeliverEventToSubscription implements ShouldQueue
{
    use Queueable;

    public const TIMEOUT_SECONDS = 15;
    public const BODY_SNIPPET_BYTES = 4096;

    public const MAX_ATTEMPTS = 6;

    /**
     * Backoff delays (in seconds) BEFORE attempts 2..MAX_ATTEMPTS.
     * Length = MAX_ATTEMPTS - 1.
     *
     * Attempt 1 fires immediately. After it fails, the next attempt is
     * scheduled BACKOFF[0] seconds later. After attempt N fails, the next
     * is scheduled at BACKOFF[N-1].
     *
     *   attempt 1 -> +30s   -> attempt 2
     *   attempt 2 -> +2m    -> attempt 3
     *   attempt 3 -> +10m   -> attempt 4
     *   attempt 4 -> +1h    -> attempt 5
     *   attempt 5 -> +6h    -> attempt 6
     *   attempt 6 -> dead
     */
    public const BACKOFF = [30, 120, 600, 3600, 21600];

    public const CIRCUIT_BREAKER_THRESHOLD = 8;

    public int $tries = 1;

    public function __construct(
        public readonly string $eventId,
        public readonly string $subscriptionId,
    ) {}

    public function handle(
        WebhookSigner $signer,
        SsrfGuard $ssrf,
    ): void {
        $event = Event::find($this->eventId);
        $subscription = Subscription::find($this->subscriptionId);

        if ($event === null || $subscription === null) {
            return;
        }

        $delivery = $this->findOrCreateDelivery($event, $subscription);

        // Terminal states are never replayed by the worker.
        if (in_array($delivery->status, [Delivery::STATUS_SUCCESS, Delivery::STATUS_DEAD], true)) {
            return;
        }

        if (! $subscription->isActive()) {
            $this->markDead($delivery, statusCode: null);

            return;
        }

        $body = json_encode([
            'id' => $event->id,
            'type' => $event->type,
            'created_at' => $event->created_at->toIso8601String(),
            'data' => $event->payload,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $signed = $signer->sign($subscription->signing_secret, $body);
        $attemptNumber = $delivery->attempts_made + 1;
        $start = microtime(true);

        $blocked = $ssrf->check($subscription->url);
        if ($blocked !== null) {
            $this->recordAttempt(
                delivery: $delivery,
                attemptNumber: $attemptNumber,
                signature: $signed['header'],
                responseStatus: null,
                responseHeaders: null,
                responseBodySnippet: null,
                latencyMs: 0,
                errorCode: 'ssrf:'.$blocked,
            );
            // SSRF block is never retryable — bad URL is on the user.
            $this->markDead($delivery, statusCode: null);
            $this->bumpCircuitBreaker($subscription);

            return;
        }

        try {
            $response = Http::withHeaders([
                'X-Webhook-Signature' => $signed['header'],
                'X-Webhook-Event-Id' => $event->id,
                'X-Webhook-Event-Type' => $event->type,
                'X-Webhook-Timestamp' => (string) $signed['timestamp'],
                'Content-Type' => 'application/json',
                'User-Agent' => 'WebhookRelay/0.3 (+https://webhook-relay.dcsuniverse.com)',
            ])
                ->withBody($body, 'application/json')
                ->timeout(self::TIMEOUT_SECONDS)
                ->retry(0)
                ->post($subscription->url);

            $latencyMs = (int) round((microtime(true) - $start) * 1000);

            $this->recordAttempt(
                delivery: $delivery,
                attemptNumber: $attemptNumber,
                signature: $signed['header'],
                responseStatus: $response->status(),
                responseHeaders: $this->whitelistHeaders($response),
                responseBodySnippet: Str::limit((string) $response->body(), self::BODY_SNIPPET_BYTES, ''),
                latencyMs: $latencyMs,
                errorCode: null,
            );

            $this->finalize($delivery, $subscription, $response->status());
        } catch (ConnectionException $e) {
            $latencyMs = (int) round((microtime(true) - $start) * 1000);
            $this->recordAttempt(
                delivery: $delivery,
                attemptNumber: $attemptNumber,
                signature: $signed['header'],
                responseStatus: null,
                responseHeaders: null,
                responseBodySnippet: Str::limit($e->getMessage(), self::BODY_SNIPPET_BYTES, ''),
                latencyMs: $latencyMs,
                errorCode: 'connection_error',
            );
            // Connection errors are 5xx-shaped: retryable.
            $this->finalize($delivery, $subscription, statusCode: null, transientError: true);
        }
    }

    /**
     * Finalize an attempt outcome. Branches:
     *   - 2xx: success, reset circuit breaker.
     *   - 4xx: dead (subscriber problem; not retried), bump breaker.
     *   - 5xx / transient: retry with backoff if attempts remain, else dead.
     */
    private function finalize(
        Delivery $delivery,
        Subscription $subscription,
        ?int $statusCode,
        bool $transientError = false,
    ): void {
        $delivery->refresh();

        if ($statusCode !== null && $statusCode >= 200 && $statusCode < 300) {
            $delivery->update([
                'status' => Delivery::STATUS_SUCCESS,
                'final_status_code' => $statusCode,
                'completed_at' => now(),
                'next_attempt_at' => null,
            ]);
            $this->resetCircuitBreaker($subscription);

            return;
        }

        // 4xx: subscriber error, terminal, no retries.
        if ($statusCode !== null && $statusCode >= 400 && $statusCode < 500) {
            $this->markDead($delivery, statusCode: $statusCode);
            $this->bumpCircuitBreaker($subscription);

            return;
        }

        // 5xx or transient (timeout/connection): retry with backoff.
        $this->bumpCircuitBreaker($subscription);

        if ($delivery->attempts_made >= self::MAX_ATTEMPTS) {
            $this->markDead($delivery, statusCode: $statusCode);

            return;
        }

        $delaySeconds = self::BACKOFF[$delivery->attempts_made - 1] ?? null;
        if ($delaySeconds === null) {
            // Defensive: out of bounds means we should be dead by now.
            $this->markDead($delivery, statusCode: $statusCode);

            return;
        }

        $nextAt = now()->addSeconds($delaySeconds);
        $delivery->update([
            'status' => Delivery::STATUS_PENDING,
            'final_status_code' => $statusCode,
            'next_attempt_at' => $nextAt,
        ]);

        self::dispatch($this->eventId, $this->subscriptionId)
            ->delay($nextAt);
    }

    private function markDead(Delivery $delivery, ?int $statusCode): void
    {
        $delivery->update([
            'status' => Delivery::STATUS_DEAD,
            'final_status_code' => $statusCode,
            'completed_at' => now(),
            'next_attempt_at' => null,
        ]);
    }

    /**
     * Atomically increment consecutive_failures and, if the threshold is
     * crossed for the first time, pause the subscription.
     */
    private function bumpCircuitBreaker(Subscription $subscription): void
    {
        DB::transaction(function () use ($subscription) {
            $fresh = Subscription::lockForUpdate()->find($subscription->id);
            if ($fresh === null) {
                return;
            }

            $fresh->increment('consecutive_failures');

            if (
                $fresh->isActive()
                && $fresh->consecutive_failures >= self::CIRCUIT_BREAKER_THRESHOLD
            ) {
                $fresh->update([
                    'state' => Subscription::STATE_PAUSED,
                    'paused_at' => now(),
                ]);
            }
        });
    }

    private function resetCircuitBreaker(Subscription $subscription): void
    {
        Subscription::where('id', $subscription->id)
            ->where('consecutive_failures', '>', 0)
            ->update(['consecutive_failures' => 0]);
    }

    private function findOrCreateDelivery(Event $event, Subscription $subscription): Delivery
    {
        return Delivery::firstOrCreate(
            ['event_id' => $event->id, 'subscription_id' => $subscription->id],
            [
                'workspace_id' => $event->workspace_id,
                'status' => Delivery::STATUS_PENDING,
                'attempts_made' => 0,
            ],
        );
    }

    private function recordAttempt(
        Delivery $delivery,
        int $attemptNumber,
        string $signature,
        ?int $responseStatus,
        ?array $responseHeaders,
        ?string $responseBodySnippet,
        int $latencyMs,
        ?string $errorCode,
    ): void {
        DeliveryAttempt::create([
            'delivery_id' => $delivery->id,
            'attempt_number' => $attemptNumber,
            'request_signature' => $signature,
            'response_status' => $responseStatus,
            'response_headers' => $responseHeaders,
            'response_body_snippet' => $responseBodySnippet,
            'latency_ms' => $latencyMs,
            'error_code' => $errorCode,
            'attempted_at' => now(),
        ]);

        $delivery->increment('attempts_made');
    }

    private function whitelistHeaders(Response $response): array
    {
        $keep = [
            'content-type',
            'content-length',
            'server',
            'date',
            'x-request-id',
            'x-correlation-id',
            'cache-control',
        ];

        $kept = [];
        foreach ($response->headers() as $name => $values) {
            if (in_array(strtolower($name), $keep, true)) {
                $kept[$name] = is_array($values) ? implode(', ', $values) : $values;
            }
        }

        return $kept;
    }
}
