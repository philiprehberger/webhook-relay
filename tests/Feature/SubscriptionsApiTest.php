<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Subscription;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionsApiTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspaceA;
    private Workspace $workspaceB;
    private string $keyA;
    private string $keyB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspaceA = Workspace::create(['name' => 'A', 'slug' => 'a']);
        $this->workspaceB = Workspace::create(['name' => 'B', 'slug' => 'b']);
        [, $this->keyA] = ApiKey::mint($this->workspaceA, 'test', 'a');
        [, $this->keyB] = ApiKey::mint($this->workspaceB, 'test', 'b');
    }

    public function test_create_returns_201_with_signing_secret_visible_once(): void
    {
        $response = $this->withToken($this->keyA)->postJson('/v1/subscriptions', [
            'name' => 'orders inbound',
            'url' => 'https://example.com/hooks/orders',
            'event_filter' => 'order.*',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'url', 'event_filter', 'state', 'signing_secret', 'consecutive_failures']);
        $this->assertStringStartsWith('whsec_', $response->json('signing_secret'));
    }

    public function test_create_rejects_non_https_url(): void
    {
        $response = $this->withToken($this->keyA)->postJson('/v1/subscriptions', [
            'url' => 'http://example.com/hooks',
        ]);

        $response->assertStatus(400);
    }

    public function test_index_is_workspace_scoped(): void
    {
        Subscription::create([
            'workspace_id' => $this->workspaceA->id,
            'url' => 'https://a.example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
        ]);
        Subscription::create([
            'workspace_id' => $this->workspaceB->id,
            'url' => 'https://b.example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
        ]);

        $a = $this->withToken($this->keyA)->getJson('/v1/subscriptions');
        $a->assertOk();
        $this->assertCount(1, $a->json('data'));
        $this->assertStringContainsString('a.example.com', $a->json('data.0.url'));
    }

    public function test_show_returns_subscription_without_secret(): void
    {
        $sub = Subscription::create([
            'workspace_id' => $this->workspaceA->id,
            'url' => 'https://example.com/hooks',
            'signing_secret' => 'whsec_super_secret',
        ]);

        $response = $this->withToken($this->keyA)->getJson("/v1/subscriptions/{$sub->id}");

        $response->assertOk();
        $response->assertJsonMissing(['signing_secret']);
        $this->assertNull($response->json('signing_secret'));
    }

    public function test_show_404s_across_workspaces(): void
    {
        $sub = Subscription::create([
            'workspace_id' => $this->workspaceB->id,
            'url' => 'https://example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
        ]);

        $this->withToken($this->keyA)->getJson("/v1/subscriptions/{$sub->id}")->assertStatus(404);
    }

    public function test_update_changes_url_and_filter(): void
    {
        $sub = Subscription::create([
            'workspace_id' => $this->workspaceA->id,
            'url' => 'https://old.example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
            'event_filter' => '*',
        ]);

        $response = $this->withToken($this->keyA)->patchJson("/v1/subscriptions/{$sub->id}", [
            'url' => 'https://new.example.com/hooks',
            'event_filter' => 'order.*',
        ]);

        $response->assertOk();
        $this->assertSame('https://new.example.com/hooks', $response->json('url'));
        $this->assertSame('order.*', $response->json('event_filter'));
    }

    public function test_delete_removes_the_subscription(): void
    {
        $sub = Subscription::create([
            'workspace_id' => $this->workspaceA->id,
            'url' => 'https://example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
        ]);

        $this->withToken($this->keyA)->deleteJson("/v1/subscriptions/{$sub->id}")->assertStatus(204);
        $this->assertDatabaseMissing('subscriptions', ['id' => $sub->id]);
    }

    public function test_pause_and_resume_transition_state(): void
    {
        $sub = Subscription::create([
            'workspace_id' => $this->workspaceA->id,
            'url' => 'https://example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
            'consecutive_failures' => 5,
        ]);

        $this->withToken($this->keyA)->postJson("/v1/subscriptions/{$sub->id}/pause")
            ->assertOk()
            ->assertJsonPath('state', 'paused');

        $this->withToken($this->keyA)->postJson("/v1/subscriptions/{$sub->id}/resume")
            ->assertOk()
            ->assertJsonPath('state', 'active')
            ->assertJsonPath('consecutive_failures', 0);
    }

    public function test_rotate_secret_returns_new_plaintext_and_preserves_previous(): void
    {
        $original = Subscription::generateSecret();
        $sub = Subscription::create([
            'workspace_id' => $this->workspaceA->id,
            'url' => 'https://example.com/hooks',
            'signing_secret' => $original,
        ]);

        $response = $this->withToken($this->keyA)->postJson("/v1/subscriptions/{$sub->id}/rotate-secret");

        $response->assertOk();
        $this->assertStringStartsWith('whsec_', $response->json('signing_secret'));
        $this->assertNotSame($original, $response->json('signing_secret'));

        $sub->refresh();
        $this->assertSame($original, $sub->previous_signing_secret);
        $this->assertNotNull($sub->secret_rotated_at);
    }
}
