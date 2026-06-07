<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Server-Sent Events stream of new deliveries for the workspace identified
 * by ?key=. EventSource can't set Authorization headers easily, so the key
 * is on the query string. Short stream cap (60s) keeps the connection
 * cheap under mod_php; the frontend reconnects automatically.
 */
class EchoStreamController extends Controller
{
    private const MAX_DURATION_SECONDS = 60;
    private const POLL_INTERVAL_SECONDS = 1;

    public function __invoke(Request $request): StreamedResponse
    {
        $token = (string) $request->query('key', '');
        $apiKey = ApiKey::findByPlaintext($token);

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
                $send('error', ['detail' => 'Provide ?key= with a valid sandbox / live / test key.']);

                return;
            }

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
        }, 200, $headers);
    }
}
