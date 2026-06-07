<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Workspace;
use App\Services\SsrfGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebhookTestEndpointTest extends TestCase
{
    use RefreshDatabase;

    private string $key;

    protected function setUp(): void
    {
        parent::setUp();

        $workspace = Workspace::create(['name' => 'W', 'slug' => 'w']);
        [, $this->key] = ApiKey::mint($workspace, 'test', 'k');

        // Bypass SSRF guard for example.com (DNS fails in test env).
        $this->app->instance(SsrfGuard::class, new class extends SsrfGuard {
            public function check(string $url): ?string
            {
                return null;
            }
        });
    }

    public function test_sends_signed_request_and_returns_receiver_response(): void
    {
        Http::fake(['echo.example.com/*' => Http::response('{"ack":true}', 200)]);

        $response = $this->withToken($this->key)->postJson('/v1/webhooks/test', [
            'url' => 'https://echo.example.com/inbound',
            'payload' => ['hello' => 'world'],
        ]);

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('status', 200);
        $response->assertJsonStructure(['signature_sent', 'secret_used', 'latency_ms', 'response_body_snippet']);

        Http::assertSent(function (HttpRequest $req) {
            return $req->hasHeader('X-Webhook-Signature')
                && $req->hasHeader('X-Webhook-Timestamp')
                && str_starts_with($req->header('X-Webhook-Signature')[0], 't=');
        });
    }

    public function test_uses_caller_supplied_secret_when_provided(): void
    {
        Http::fake(['echo.example.com/*' => Http::response('ok', 200)]);

        $response = $this->withToken($this->key)->postJson('/v1/webhooks/test', [
            'url' => 'https://echo.example.com/inbound',
            'secret' => 'whsec_my_known_secret',
        ]);

        $response->assertOk();
        $response->assertJsonPath('secret_used', 'whsec_my_known_secret');
    }

    public function test_rejects_non_https_url(): void
    {
        $this->withToken($this->key)
            ->postJson('/v1/webhooks/test', ['url' => 'http://echo.example.com/inbound'])
            ->assertStatus(400);
    }

    public function test_ssrf_blocked_url_returns_400(): void
    {
        // Use the real SsrfGuard for this test.
        $this->app->instance(SsrfGuard::class, new SsrfGuard());

        $this->withToken($this->key)
            ->postJson('/v1/webhooks/test', ['url' => 'https://127.0.0.1/internal'])
            ->assertStatus(400);
    }

    public function test_returns_ok_false_on_5xx_without_throwing(): void
    {
        Http::fake(['echo.example.com/*' => Http::response('boom', 503)]);

        $response = $this->withToken($this->key)->postJson('/v1/webhooks/test', [
            'url' => 'https://echo.example.com/inbound',
        ]);

        $response->assertOk();
        $response->assertJsonPath('ok', false);
        $response->assertJsonPath('status', 503);
    }
}
