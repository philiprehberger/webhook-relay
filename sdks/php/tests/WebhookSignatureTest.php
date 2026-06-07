<?php

namespace WebhookRelay\Client\Tests;

use PHPUnit\Framework\TestCase;
use WebhookRelay\Client\WebhookSignature;

class WebhookSignatureTest extends TestCase
{
    private const SECRET = 'whsec_test_round_trip';

    public function test_round_trips_against_canonical_signer(): void
    {
        $body = '{"event":"order.created","amount":100}';
        $header = $this->sign(self::SECRET, $body);

        $this->assertTrue(WebhookSignature::verify(self::SECRET, $body, $header));
    }

    public function test_rejects_tampered_body(): void
    {
        $header = $this->sign(self::SECRET, 'original body');
        $this->assertFalse(WebhookSignature::verify(self::SECRET, 'tampered body', $header));
    }

    public function test_rejects_wrong_secret(): void
    {
        $header = $this->sign(self::SECRET, 'body');
        $this->assertFalse(WebhookSignature::verify('whsec_wrong', 'body', $header));
    }

    public function test_rejects_old_timestamp(): void
    {
        $header = $this->sign(self::SECRET, 'body', time() - 1000);
        $this->assertFalse(WebhookSignature::verify(self::SECRET, 'body', $header, 300));
    }

    public function test_rejects_malformed_header(): void
    {
        $this->assertFalse(WebhookSignature::verify(self::SECRET, 'body', 'garbage'));
        $this->assertFalse(WebhookSignature::verify(self::SECRET, 'body', 't=abc,v1=xyz'));
        $this->assertFalse(WebhookSignature::verify(self::SECRET, 'body', 'v1=xyz'));
    }

    private function sign(string $secret, string $body, ?int $ts = null): string
    {
        $ts ??= time();
        $hex = hash_hmac('sha256', $ts.'.'.$body, $secret);

        return "t={$ts},v1={$hex}";
    }
}
