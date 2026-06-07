<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    private string $key;

    protected function setUp(): void
    {
        parent::setUp();

        $workspace = Workspace::create(['name' => 'W', 'slug' => 'w']);
        [, $this->key] = ApiKey::mint($workspace, 'test', 'k');

        RateLimiter::clear("wsr:{$workspace->id}");
    }

    public function test_emits_ratelimit_headers_on_success(): void
    {
        $response = $this->withToken($this->key)->getJson('/v1/events');

        $response->assertOk();
        $response->assertHeader('X-RateLimit-Limit', '100');
        $this->assertTrue($response->headers->has('X-RateLimit-Remaining'));
        $this->assertTrue($response->headers->has('X-RateLimit-Reset'));
    }

    public function test_returns_429_problem_json_after_threshold(): void
    {
        // Burn through the bucket.
        for ($i = 0; $i < 100; $i++) {
            $this->withToken($this->key)->getJson('/v1/events');
        }

        $response = $this->withToken($this->key)->getJson('/v1/events');

        $response->assertStatus(429);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertHeader('X-RateLimit-Remaining', '0');
        $response->assertJsonPath('status', 429);
    }

    public function test_healthz_is_not_rate_limited(): void
    {
        for ($i = 0; $i < 200; $i++) {
            $this->getJson('/v1/healthz')->assertOk();
        }
    }
}
