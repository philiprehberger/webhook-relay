<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Delivery;
use App\Models\DeliveryAttempt;
use App\Models\Event;
use App\Models\Subscription;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhilipRehberger\Interchange\Tracing\TraceContext;
use Tests\TestCase;

/**
 * Plan T-7.1 — the trace endpoint's two load-bearing constraints.
 */
class TraceEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_events_and_deliveries_for_a_trace(): void
    {
        [$workspace, $token] = $this->workspace();
        $traceId = TraceContext::newTraceId();
        $event = $this->event($workspace, $traceId, ['secret_field' => 'must-not-appear']);
        $this->delivery($workspace, $event, $traceId);

        $response = $this->getJson("/v1/traces/{$traceId}", $this->authed($token))->assertOk();

        $response->assertJsonPath('trace_id', $traceId);
        $this->assertCount(1, $response->json('events'));
        $this->assertCount(1, $response->json('deliveries'));
    }

    public function test_it_never_returns_payload_bodies(): void
    {
        // SPEC §3.4 — trace-shaped endpoints are metadata only, for EVERY
        // class. Adding a second, laxer path to the same data is how exposure
        // rules get quietly undone.
        [$workspace, $token] = $this->workspace();
        $traceId = TraceContext::newTraceId();
        $event = $this->event($workspace, $traceId, ['email' => 'real.person@example.test']);
        $this->delivery($workspace, $event, $traceId);

        $response = $this->getJson("/v1/traces/{$traceId}", $this->authed($token))->assertOk();

        $this->assertStringNotContainsString('real.person', $response->getContent());
        $this->assertArrayNotHasKey('payload', $response->json('events.0'));
        $this->assertArrayNotHasKey('data', $response->json('events.0'));
        $this->assertArrayNotHasKey('response_body_snippet', $response->json('deliveries.0.attempts.0'));

        // Size without content: enough to see a payload was large.
        $this->assertGreaterThan(0, $response->json('events.0.payload_bytes'));
    }

    public function test_a_trace_from_another_workspace_is_indistinguishable_from_a_missing_one(): void
    {
        // 128-bit ids are hard to guess, but "hard to guess" is not
        // authorization. Existence is itself information.
        [$owner] = $this->workspace('owner');
        [, $otherToken] = $this->workspace('other');

        $traceId = TraceContext::newTraceId();
        $this->event($owner, $traceId, ['a' => 1]);

        $this->getJson("/v1/traces/{$traceId}", $this->authed($otherToken))
            ->assertStatus(404)
            ->assertJsonPath('title', 'Not found');

        $this->getJson('/v1/traces/'.TraceContext::newTraceId(), $this->authed($otherToken))
            ->assertStatus(404)
            ->assertJsonPath('title', 'Not found');
    }

    public function test_a_malformed_trace_id_is_rejected(): void
    {
        [, $token] = $this->workspace();

        $this->getJson('/v1/traces/not-a-trace-id', $this->authed($token))
            ->assertStatus(400)
            ->assertJsonPath('title', 'Invalid trace id');
    }

    public function test_the_endpoint_requires_authentication(): void
    {
        $this->getJson('/v1/traces/'.TraceContext::newTraceId())->assertStatus(401);
    }

    public function test_a_forged_scenario_class_changes_nothing_about_exposure(): void
    {
        // D-1: mnl_class is advisory and forgeable. Claiming `scenario` must
        // not unlock payload bodies.
        [$workspace, $token] = $this->workspace();
        $traceId = TraceContext::newTraceId();
        $this->event($workspace, $traceId, ['email' => 'real.person@example.test']);

        $response = $this->getJson("/v1/traces/{$traceId}", $this->authed($token) + [
            'tracestate' => 'mnl_class=scenario',
        ])->assertOk();

        $this->assertStringNotContainsString('real.person', $response->getContent());
    }

    private function workspace(string $slug = 'ws'): array
    {
        $workspace = Workspace::create(['name' => ucfirst($slug), 'slug' => $slug.'-'.uniqid()]);
        [, $token] = ApiKey::mint($workspace, 'test', $slug.'-key');

        return [$workspace, $token];
    }

    private function event(Workspace $workspace, string $traceId, array $payload): Event
    {
        return Event::create([
            'workspace_id' => $workspace->id,
            'type' => 'contact.created',
            'payload' => $payload,
            'correlation_id' => 'corr_'.uniqid(),
            'trace_id' => $traceId,
            'trace_class' => 'production',
        ]);
    }

    private function delivery(Workspace $workspace, Event $event, string $traceId): Delivery
    {
        $subscription = Subscription::create([
            'workspace_id' => $workspace->id,
            'url' => 'https://example.com/hook',
            'signing_secret' => 'secret-value-at-least-sixteen',
            'event_types' => ['contact.created'],
        ]);

        $delivery = Delivery::create([
            'event_id' => $event->id,
            'subscription_id' => $subscription->id,
            'workspace_id' => $workspace->id,
            'trace_id' => $traceId,
            'status' => Delivery::STATUS_SUCCESS,
            'attempts_made' => 1,
            'final_status_code' => 200,
        ]);

        // A real attempt carrying a response body, so the leak assertion has
        // something that COULD leak rather than passing on an empty set.
        DeliveryAttempt::create([
            'delivery_id' => $delivery->id,
            'attempt_number' => 1,
            'request_signature' => 't=1,v1=abc',
            'response_status' => 200,
            'response_body_snippet' => 'downstream said real.person@example.test',
            'latency_ms' => 42,
            'attempted_at' => now(),
        ]);

        return $delivery;
    }

    private function authed(string $token): array
    {
        return ['Authorization' => 'Bearer '.$token, 'Accept' => 'application/json'];
    }
}
