<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ProblemResponse;
use App\Services\SsrfGuard;
use App\Services\WebhookSigner;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * "Ping your URL" endpoint that powers the docs-site try-it widget.
 * Sends one synchronously signed sample event, returns the receiver's
 * response. No Subscription / Delivery rows are created — this is a probe.
 */
class WebhookTestController
{
    private const TIMEOUT_SECONDS = 10;
    private const BODY_SNIPPET_BYTES = 4096;

    public function __invoke(Request $request, WebhookSigner $signer, SsrfGuard $ssrf): JsonResponse|ProblemResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'starts_with:https://', 'max:2048'],
            'secret' => ['nullable', 'string', 'min:8', 'max:128'],
            'payload' => ['nullable', 'array'],
            'event_type' => ['nullable', 'string', 'regex:/^[a-z0-9._-]{1,128}$/'],
        ]);

        $blocked = $ssrf->check($validated['url']);
        if ($blocked !== null) {
            return new ProblemResponse(
                status: 400,
                title: 'URL rejected by SSRF guard',
                detail: "Outbound delivery to that URL is blocked: {$blocked}.",
            );
        }

        $secret = $validated['secret'] ?? 'whsec_test_'.Str::random(32);
        $body = json_encode([
            'id' => '01TESTEVENT'.Str::upper(Str::random(15)),
            'type' => $validated['event_type'] ?? 'test.ping',
            'created_at' => now()->toIso8601String(),
            'data' => $validated['payload'] ?? ['hello' => 'from-webhook-relay'],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $signed = $signer->sign($secret, $body);
        $start = microtime(true);

        try {
            $response = Http::withHeaders([
                'X-Webhook-Signature' => $signed['header'],
                'X-Webhook-Event-Type' => $validated['event_type'] ?? 'test.ping',
                'X-Webhook-Timestamp' => (string) $signed['timestamp'],
                'Content-Type' => 'application/json',
                'User-Agent' => 'WebhookRelay/0.3 test-probe',
            ])
                ->withBody($body, 'application/json')
                ->timeout(self::TIMEOUT_SECONDS)
                ->retry(0)
                ->post($validated['url']);

            return response()->json([
                'ok' => $response->successful(),
                'status' => $response->status(),
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'response_body_snippet' => Str::limit((string) $response->body(), self::BODY_SNIPPET_BYTES, ''),
                'signature_sent' => $signed['header'],
                'secret_used' => $secret,
            ]);
        } catch (ConnectionException $e) {
            return response()->json([
                'ok' => false,
                'status' => null,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'response_body_snippet' => null,
                'signature_sent' => $signed['header'],
                'secret_used' => $secret,
                'error_code' => 'connection_error',
                'error_detail' => Str::limit($e->getMessage(), 512, ''),
            ]);
        }
    }
}
