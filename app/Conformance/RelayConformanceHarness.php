<?php

namespace App\Conformance;

use App\Models\Event;
use App\Services\WebhookSigner;
use PhilipRehberger\Interchange\Conformance\ConformanceHarness;
use PhilipRehberger\Interchange\Signing\StandardWebhooksScheme;

/**
 * Plan 7.12 / D-4 — Webhook Relay's conformance adapter.
 */
class RelayConformanceHarness implements ConformanceHarness
{
    public function serviceSlug(): string
    {
        return 'webhook-relay';
    }

    public function inboundTracePath(): string
    {
        return '/v1/healthz';
    }

    public function supportedSchemes(): array
    {
        return ['webhook-relay-v0', 'standard-webhooks'];
    }

    public function triggerSignedDelivery(string $scheme): array
    {
        $body = json_encode([
            'id' => '01JCONFORMANCE0EVENT00001',
            'type' => 'conformance.check',
            'created_at' => now()->toIso8601String(),
            'data' => ['ok' => true],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($scheme === 'standard-webhooks') {
            $secret = StandardWebhooksScheme::generateSecret();
            $headers = (new StandardWebhooksScheme)->sign('01JCONFORMANCE0EVENT00001', $body, $secret);
        } else {
            $secret = 'conformance-secret-not-a-real-one';
            $signed = (new WebhookSigner)->sign($secret, $body);
            $headers = [
                'X-Webhook-Signature' => $signed['header'],
                'X-Webhook-Timestamp' => (string) $signed['timestamp'],
            ];
        }

        return [
            'headers' => array_change_key_case($headers),
            'body' => $body,
            'secret' => $secret,
        ];
    }

    public function dispatchTracedJob(): void
    {
        // Must genuinely enqueue: running inline proves nothing about the
        // queue boundary, which is the point of `trace.queue`.
        \App\Jobs\DeliverEventToSubscription::dispatch('01JCONFORMANCE0DELIVERY01');
    }
}
