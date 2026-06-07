<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ProblemResponse;
use App\Models\ApiKey;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Anonymous endpoint that mints a sandbox API key for the public-sandbox
 * workspace. Powers the docs-site try-it console so visitors don't have
 * to bring their own key. Per-IP rate limit, no auth required.
 *
 * Sandbox keys are downscoped at delivery time — subscriptions on them can
 * only POST to a small allowlist of receiver-testing services (see
 * SubscriptionsController and WebhookTestController).
 */
class SandboxKeysController extends Controller
{
    private const MAX_PER_IP_PER_HOUR = 5;

    public function __invoke(Request $request): JsonResponse|ProblemResponse
    {
        $ip = $request->ip() ?? 'unknown';
        $bucket = "sandbox-mint:{$ip}";

        if (RateLimiter::tooManyAttempts($bucket, self::MAX_PER_IP_PER_HOUR)) {
            return new ProblemResponse(
                status: 429,
                title: 'Too many sandbox keys',
                detail: 'Limit is '.self::MAX_PER_IP_PER_HOUR.' sandbox keys per IP per hour. Save the one you already have.',
            );
        }
        RateLimiter::hit($bucket, decaySeconds: 3600);

        $workspace = Workspace::sandbox();
        [$apiKey, $plaintext] = ApiKey::mint(
            $workspace,
            'sandbox',
            name: 'docs-site visitor',
        );

        return response()->json([
            'key' => $plaintext,
            'prefix' => $apiKey->prefix,
            'expires_in_hours' => 24,
            'allowed_receiver_hosts' => [
                'webhook.site',
                'requestbin.com',
                'httpbin.org',
            ],
            'docs' => 'https://webhook-relay.dcsuniverse.com/about',
            'notes' => 'Sandbox keys can subscribe only to the allowed receiver hosts above. Created events + deliveries auto-expire after 24 hours.',
        ], 201);
    }
}
