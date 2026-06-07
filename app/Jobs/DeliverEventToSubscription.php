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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DeliverEventToSubscription implements ShouldQueue
{
    use Queueable;

    public const TIMEOUT_SECONDS = 15;
    public const BODY_SNIPPET_BYTES = 4096;

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

        if (! $subscription->isActive()) {
            $delivery->update([
                'status' => Delivery::STATUS_DEAD,
                'completed_at' => now(),
            ]);

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
            $this->finalize($delivery, success: false, statusCode: null);

            return;
        }

        try {
            $response = Http::withHeaders([
                'X-Webhook-Signature' => $signed['header'],
                'X-Webhook-Event-Id' => $event->id,
                'X-Webhook-Event-Type' => $event->type,
                'X-Webhook-Timestamp' => (string) $signed['timestamp'],
                'Content-Type' => 'application/json',
                'User-Agent' => 'WebhookRelay/0.2 (+https://webhook-relay.dcsuniverse.com)',
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

            $this->finalize(
                $delivery,
                success: $response->successful(),
                statusCode: $response->status(),
            );
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
            $this->finalize($delivery, success: false, statusCode: null);
        }
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

    private function finalize(Delivery $delivery, bool $success, ?int $statusCode): void
    {
        $delivery->refresh();

        if ($success) {
            $delivery->update([
                'status' => Delivery::STATUS_SUCCESS,
                'final_status_code' => $statusCode,
                'completed_at' => now(),
                'next_attempt_at' => null,
            ]);

            return;
        }

        // Phase 3 scope: single attempt, no retry yet (Phase 4 adds backoff).
        $delivery->update([
            'status' => Delivery::STATUS_FAILED,
            'final_status_code' => $statusCode,
            'completed_at' => now(),
            'next_attempt_at' => null,
        ]);
    }

    /**
     * Keep only the response headers worth showing in the dashboard —
     * upstream response can include security tokens we don't want to store.
     */
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
