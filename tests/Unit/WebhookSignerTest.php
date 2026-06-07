<?php

namespace Tests\Unit;

use App\Services\WebhookSigner;
use Tests\TestCase;

class WebhookSignerTest extends TestCase
{
    public function test_sign_produces_stable_hmac(): void
    {
        $signer = new WebhookSigner();
        $result = $signer->sign(
            secret: 'whsec_test_abc',
            body: '{"x":1}',
            timestamp: 1717000000,
        );

        // Independently computable: hash_hmac('sha256', '1717000000.{"x":1}', 'whsec_test_abc')
        $expected = hash_hmac('sha256', '1717000000.{"x":1}', 'whsec_test_abc');

        $this->assertSame($expected, $result['signature']);
        $this->assertSame("t=1717000000,v1={$expected}", $result['header']);
        $this->assertSame(1717000000, $result['timestamp']);
    }

    public function test_verify_round_trips(): void
    {
        $signer = new WebhookSigner();
        $body = '{"event":"order.created","amount":100}';
        $secret = 'whsec_round_trip';

        $signed = $signer->sign($secret, $body);

        $this->assertTrue($signer->verify($secret, $body, $signed['header']));
    }

    public function test_verify_rejects_wrong_secret(): void
    {
        $signer = new WebhookSigner();
        $signed = $signer->sign('whsec_a', 'body');

        $this->assertFalse($signer->verify('whsec_b', 'body', $signed['header']));
    }

    public function test_verify_rejects_tampered_body(): void
    {
        $signer = new WebhookSigner();
        $signed = $signer->sign('whsec_x', 'original body');

        $this->assertFalse($signer->verify('whsec_x', 'tampered body', $signed['header']));
    }

    public function test_verify_rejects_old_timestamp(): void
    {
        $signer = new WebhookSigner();
        $signed = $signer->sign('whsec_x', 'body', time() - 1000);

        $this->assertFalse($signer->verify('whsec_x', 'body', $signed['header'], toleranceSeconds: 300));
    }

    public function test_verify_rejects_malformed_header(): void
    {
        $signer = new WebhookSigner();

        $this->assertFalse($signer->verify('whsec_x', 'body', 'garbage'));
        $this->assertFalse($signer->verify('whsec_x', 'body', 't=abc,v1=xyz'));
        $this->assertFalse($signer->verify('whsec_x', 'body', 'v1=xyz')); // missing t
    }
}
