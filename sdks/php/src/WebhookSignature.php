<?php

namespace WebhookRelay\Client;

/**
 * Receiver-side HMAC verifier for the Webhook Relay X-Webhook-Signature header.
 *
 * Header format: t={unix_ts},v1={hex_hmac_sha256}
 * Signed payload: "{t}.{raw_body}"
 *
 * The raw body must be the exact bytes received. json_encode(json_decode(...))
 * is NOT safe — key order or whitespace will change and break the signature.
 */
final class WebhookSignature
{
    /**
     * @param string $secret              Subscription signing secret (whsec_...).
     * @param string $body                Raw request body, exactly as received.
     * @param string $header              Value of the X-Webhook-Signature header.
     * @param int    $toleranceSeconds    Reject signatures older than this. Default 300.
     */
    public static function verify(string $secret, string $body, string $header, int $toleranceSeconds = 300): bool
    {
        if ($header === '') {
            return false;
        }

        $parts = [];
        foreach (explode(',', $header) as $segment) {
            $kv = explode('=', trim($segment), 2);
            if (count($kv) === 2) {
                $parts[$kv[0]] = $kv[1];
            }
        }

        if (! isset($parts['t'], $parts['v1'])) {
            return false;
        }

        $ts = filter_var($parts['t'], FILTER_VALIDATE_INT);
        if ($ts === false) {
            return false;
        }

        if (abs(time() - $ts) > $toleranceSeconds) {
            return false;
        }

        $expected = hash_hmac('sha256', $ts.'.'.$body, $secret);

        return hash_equals($expected, $parts['v1']);
    }
}
