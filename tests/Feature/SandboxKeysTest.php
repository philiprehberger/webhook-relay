<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SandboxKeysTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('sandbox-mint:127.0.0.1');
    }

    public function test_anonymous_mint_returns_a_sandbox_key(): void
    {
        $response = $this->postJson('/v1/sandbox/keys');

        $response->assertStatus(201);
        $response->assertJsonStructure(['key', 'prefix', 'expires_in_hours', 'allowed_receiver_hosts']);
        $this->assertStringStartsWith('whk_sandbox_', $response->json('key'));
    }

    public function test_mint_creates_or_reuses_the_public_sandbox_workspace(): void
    {
        $this->postJson('/v1/sandbox/keys')->assertStatus(201);
        $this->postJson('/v1/sandbox/keys')->assertStatus(201);

        $workspaces = Workspace::where('is_sandbox', true)->count();
        $this->assertSame(1, $workspaces);

        $keys = ApiKey::count();
        $this->assertSame(2, $keys);
    }

    public function test_mint_rate_limits_after_five_per_hour(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/v1/sandbox/keys')->assertStatus(201);
        }

        $this->postJson('/v1/sandbox/keys')
            ->assertStatus(429)
            ->assertJsonPath('status', 429);
    }

    public function test_sandbox_subscription_to_unallowed_url_is_rejected(): void
    {
        $mint = $this->postJson('/v1/sandbox/keys');
        $key = $mint->json('key');

        $this->withToken($key)
            ->postJson('/v1/subscriptions', [
                'url' => 'https://attacker.example.com/receive',
                'event_filter' => '*',
            ])
            ->assertStatus(400)
            ->assertJsonPath('title', 'URL not allowed for sandbox keys');
    }

    public function test_sandbox_subscription_to_allowed_host_works(): void
    {
        $mint = $this->postJson('/v1/sandbox/keys');
        $key = $mint->json('key');

        $this->withToken($key)
            ->postJson('/v1/subscriptions', [
                'url' => 'https://webhook.site/abc-123',
                'event_filter' => '*',
            ])
            ->assertStatus(201);
    }

    public function test_subdomain_of_allowed_host_works(): void
    {
        $mint = $this->postJson('/v1/sandbox/keys');
        $key = $mint->json('key');

        $this->withToken($key)
            ->postJson('/v1/subscriptions', [
                'url' => 'https://eo123abc.webhook.site/',
                'event_filter' => '*',
            ])
            ->assertStatus(201);
    }
}
