<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Http\Responses\ProblemResponse;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Server-Sent Events stream of new deliveries for the workspace identified
 * by ?key=. EventSource can't set Authorization headers easily, so the key
 * is on the query string; the frontend reconnects automatically when a
 * stream ends.
 *
 * Two constraints follow from where this runs, and both are load-bearing.
 *
 * **Sandbox keys only.** Apache's combined log format records the full request
 * line, so a key on the query string is written to the access log verbatim.
 * That is tolerable for a self-service token that expires in 24 hours and is
 * scoped to the public sandbox workspace; it is not tolerable for a live key.
 * This endpoint used to accept any valid key.
 *
 * **Bounded concurrency.** Each open stream holds one php-fpm child for up to
 * 60 seconds, and `pm.max_children` is 20 for a pool shared by every PHP
 * application on this host. Duration alone is not a budget — roughly twenty
 * concurrent streams would stop the whole estate serving, and sandbox keys are
 * self-service. So streams are counted and refused past a cap. An earlier
 * version of this comment reasoned about mod_php, which is not what this host
 * runs.
 */
class EchoStreamController extends Controller
{
    private const MAX_DURATION_SECONDS = 60;
    private const POLL_INTERVAL_SECONDS = 1;

    /** Concurrent streams allowed per key, and across the whole service. */
    private const MAX_STREAMS_PER_KEY = 2;
    private const MAX_STREAMS_GLOBAL = 6;

    /** Safety margin over MAX_DURATION_SECONDS so a killed worker's slot frees itself. */
    private const SLOT_TTL_SECONDS = 90;

    public function __invoke(Request $request): StreamedResponse|ProblemResponse
    {
        $token = (string) $request->query('key', '');
        $apiKey = ApiKey::findByPlaintext($token);

        if ($apiKey !== null && ! $apiKey->is_sandbox) {
            return new ProblemResponse(
                status: 403,
                title: 'Sandbox keys only',
                detail: 'This stream accepts sandbox keys only — the key travels on the query string '
                    .'and is written to access logs. Mint one at POST /v1/sandbox/keys.',
            );
        }

        if ($apiKey !== null && ! $this->claimSlot($apiKey)) {
            return new ProblemResponse(
                status: 503,
                title: 'Too many open streams',
                detail: 'The echo stream is at capacity. Retry in a few seconds — streams last at most 60s.',
            );
        }

        $headers = [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, private',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
            'Access-Control-Allow-Origin' => 'https://webhook-relay.dcsuniverse.com',
        ];

        return new StreamedResponse(function () use ($apiKey) {
            // Disable PHP output buffering so each event flushes immediately.
            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            ignore_user_abort(false);

            $send = static function (string $event, array $data): void {
                echo "event: {$event}\n";
                echo 'data: '.json_encode($data, JSON_UNESCAPED_SLASHES)."\n\n";
                @flush();
            };

            if ($apiKey === null) {
                $send('error', ['detail' => 'Provide ?key= with a valid sandbox key. Mint one at POST /v1/sandbox/keys.']);

                return;
            }

            // Whatever ends this stream — the duration cap, an aborted
            // connection, or a thrown exception — the slot must come back.
            // A leaked slot is a permanently smaller budget.
            try {
                $send('ready', [
                    'workspace' => $apiKey->workspace->slug,
                    'message' => 'Streaming new deliveries. Reconnect for the next batch.',
                ]);

                $start = time();
                $cursor = now();

                while ((time() - $start) < self::MAX_DURATION_SECONDS) {
                    if (connection_aborted()) {
                        return;
                    }

                    $deliveries = Delivery::query()
                        ->where('workspace_id', $apiKey->workspace_id)
                        ->where('updated_at', '>', $cursor)
                        ->orderBy('updated_at')
                        ->limit(20)
                        ->get();

                    foreach ($deliveries as $delivery) {
                        $send('delivery', [
                            'id' => $delivery->id,
                            'event_id' => $delivery->event_id,
                            'subscription_id' => $delivery->subscription_id,
                            'status' => $delivery->status,
                            'attempts_made' => $delivery->attempts_made,
                            'final_status_code' => $delivery->final_status_code,
                            'updated_at' => $delivery->updated_at->toIso8601String(),
                        ]);
                        $cursor = $delivery->updated_at;
                    }

                    // Keep-alive comment so proxies don't drop the connection.
                    echo ": keepalive ".time()."\n\n";
                    @flush();

                    sleep(self::POLL_INTERVAL_SECONDS);
                }

                $send('closing', ['reason' => 'max_duration_reached']);
            } finally {
                $this->releaseSlot($apiKey);
            }
        }, 200, $headers);
    }

    /**
     * Reserve one of the bounded stream slots, per key and globally.
     *
     * Redis counters with a TTL rather than a precise semaphore: if a worker is
     * killed mid-stream the increment expires on its own, so the budget heals
     * without a reaper. Slightly over-permissive under a race, which is the
     * right direction for a docs-page demo.
     */
    private function claimSlot(ApiKey $apiKey): bool
    {
        $perKey = "echo:streams:{$apiKey->id}";
        $global = 'echo:streams:global';

        if ((int) Cache::get($global, 0) >= self::MAX_STREAMS_GLOBAL) {
            return false;
        }

        if ((int) Cache::get($perKey, 0) >= self::MAX_STREAMS_PER_KEY) {
            return false;
        }

        Cache::add($perKey, 0, self::SLOT_TTL_SECONDS);
        Cache::add($global, 0, self::SLOT_TTL_SECONDS);
        Cache::increment($perKey);
        Cache::increment($global);

        return true;
    }

    private function releaseSlot(ApiKey $apiKey): void
    {
        foreach (["echo:streams:{$apiKey->id}", 'echo:streams:global'] as $key) {
            if ((int) Cache::get($key, 0) > 0) {
                Cache::decrement($key);
            }
        }
    }
}
