<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Workspace;
use App\Services\SsrfGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The SSRF guard and the sandbox allowlist both validate the URL they are
 * handed. Following a redirect reaches an address neither of them saw.
 *
 * The concrete chain this closes: `httpbin.org` is on the sandbox allowlist and
 * offers `/redirect-to?url=…`, sandbox keys are self-service and unauthenticated
 * at POST /v1/sandbox/keys, and the probe returns `response_body_snippet` — so
 * with redirects on, an anonymous caller could read internal services.
 */
class RedirectRefusalTest extends TestCase
{
    use RefreshDatabase;

    private string $key;

    protected function setUp(): void
    {
        parent::setUp();

        $workspace = Workspace::create(['name' => 'W', 'slug' => 'w-redirect']);
        [, $this->key] = ApiKey::mint($workspace, 'test', 'k');

        $this->app->instance(SsrfGuard::class, new class extends SsrfGuard
        {
            public function check(string $url): ?string
            {
                return null;
            }
        });
    }

    public function test_the_probe_refuses_a_redirect_instead_of_following_it(): void
    {
        Http::fake([
            'redirector.example.com/*' => Http::response('', 302, ['Location' => 'http://127.0.0.1:8002/embed']),
            // If the client followed the redirect this would be reached and the
            // body would come back in response_body_snippet.
            '127.0.0.1:*' => Http::response('INTERNAL-SERVICE-SECRET', 200),
        ]);

        $response = $this->withToken($this->key)->postJson('/v1/webhooks/test', [
            'url' => 'https://redirector.example.com/go',
        ])->assertOk();

        $response->assertJsonPath('ok', false);
        $response->assertJsonPath('error_code', 'redirect_refused');
        $this->assertNull($response->json('response_body_snippet'));
        $response->assertDontSee('INTERNAL-SERVICE-SECRET');
    }

    public function test_a_normal_response_is_still_returned_intact(): void
    {
        Http::fake(['ok.example.com/*' => Http::response('{"ack":true}', 200)]);

        $this->withToken($this->key)->postJson('/v1/webhooks/test', [
            'url' => 'https://ok.example.com/inbound',
        ])->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('status', 200);
    }
}
