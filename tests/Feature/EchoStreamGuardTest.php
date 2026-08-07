<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Two constraints on the SSE stream, both structural rather than cosmetic.
 *
 * The key travels on the query string, which Apache writes to the access log
 * verbatim — so only self-service sandbox keys are accepted, never live ones.
 *
 * And each open stream holds one php-fpm child for up to 60 seconds against a
 * pool of 20 shared with every PHP application on the host, so concurrency is
 * budgeted, not just duration.
 */
class EchoStreamGuardTest extends TestCase
{
    use RefreshDatabase;

    private function workspace(string $slug): Workspace
    {
        return Workspace::create(['name' => 'W', 'slug' => $slug]);
    }

    public function test_a_live_key_is_refused(): void
    {
        [, $live] = ApiKey::mint($this->workspace('echo-live'), 'live', 'k');

        $this->getJson('/v1/echo/stream?key='.$live)
            ->assertStatus(403)
            ->assertJsonPath('title', 'Sandbox keys only');
    }

    public function test_a_test_key_is_refused(): void
    {
        [, $test] = ApiKey::mint($this->workspace('echo-test'), 'test', 'k');

        $this->getJson('/v1/echo/stream?key='.$test)->assertStatus(403);
    }

    public function test_a_sandbox_key_is_accepted(): void
    {
        [, $sandbox] = ApiKey::mint($this->workspace('echo-sandbox'), 'sandbox', 'k');

        $this->getJson('/v1/echo/stream?key='.$sandbox)->assertOk();
    }

    public function test_streams_are_refused_once_the_global_budget_is_exhausted(): void
    {
        [, $sandbox] = ApiKey::mint($this->workspace('echo-cap'), 'sandbox', 'k');

        Cache::put('echo:streams:global', 99, 90);

        $this->getJson('/v1/echo/stream?key='.$sandbox)
            ->assertStatus(503)
            ->assertJsonPath('title', 'Too many open streams');
    }

    public function test_streams_are_refused_once_the_per_key_budget_is_exhausted(): void
    {
        [$key, $sandbox] = ApiKey::mint($this->workspace('echo-perkey'), 'sandbox', 'k');

        Cache::put("echo:streams:{$key->id}", 99, 90);

        $this->getJson('/v1/echo/stream?key='.$sandbox)->assertStatus(503);
    }
}
