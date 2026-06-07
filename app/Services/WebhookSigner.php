<?php

namespace App\Services;

/**
 * HMAC-SHA256 signing for outbound webhook deliveries.
 *
 * Header format (Stripe / Svix style):
 *   X-Webhook-Signature: t={unix_ts},v1={hex_hmac_sha256}
 *
 * Signed string: "{timestamp}.{raw_body}"
 *
 * Receivers verify by recomputing the HMAC over "{t}.{body}" using their
 * stored secret and comparing in constant time to v1.
 */
class WebhookSigner
{
    public function sign(string $secret, string $body, ?int $timestamp = null): array
    {
        $timestamp ??= time();
        $payload = $timestamp.'.'.$body;
        $hex = hash_hmac('sha256', $payload, $secret);

        return [
            'timestamp' => $timestamp,
            'signature' => $hex,
            'header' => "t={$timestamp},v1={$hex}",
        ];
    }

    /**
     * Verify a signature header against a body + secret. Constant-time.
     * Returns true on match.
     */
    public function verify(string $secret, string $body, string $header, int $toleranceSeconds = 300): bool
    {
        $parsed = $this->parseHeader($header);
        if ($parsed === null) {
            return false;
        }

        ['timestamp' => $timestamp, 'signature' => $providedSig] = $parsed;

        if (abs(time() - $timestamp) > $toleranceSeconds) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$body, $secret);

        return hash_equals($expected, $providedSig);
    }

    /**
     * @return array{timestamp: int, signature: string}|null
     */
    private function parseHeader(string $header): ?array
    {
        $parts = [];
        foreach (explode(',', $header) as $segment) {
            $kv = explode('=', trim($segment), 2);
            if (count($kv) === 2) {
                $parts[$kv[0]] = $kv[1];
            }
        }

        if (! isset($parts['t']) || ! isset($parts['v1'])) {
            return null;
        }

        $ts = filter_var($parts['t'], FILTER_VALIDATE_INT);
        if ($ts === false) {
            return null;
        }

        return ['timestamp' => $ts, 'signature' => $parts['v1']];
    }
}
