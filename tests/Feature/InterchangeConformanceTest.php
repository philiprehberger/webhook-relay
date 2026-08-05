<?php

namespace Tests\Feature;

use App\Conformance\RelayConformanceHarness;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhilipRehberger\Interchange\Tracing\TraceContext;
use PhilipRehberger\InterchangeConformance\ConformanceSuite;
use Tests\TestCase;

/** Plan T-7.3 — measured by the same shared suite as every other service. */
class InterchangeConformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_shared_conformance_suite_passes(): void
    {
        $report = (new ConformanceSuite(new RelayConformanceHarness))->run();

        $this->assertTrue($report->isConformant(), $report->toMatrix());
        $this->assertSame('pass', $report->results()['sig.standard-webhooks.sign']['state']);
        $this->assertSame('pass', $report->results()['sig.secret.decode']['state']);
    }

    public function test_trace_context_is_accepted_and_echoed(): void
    {
        $context = TraceContext::start();

        $echoed = $this->withHeaders(['traceparent' => $context->toTraceparent()])
            ->getJson('/v1/healthz')
            ->headers->get('traceparent');

        $this->assertNotNull($echoed, 'no traceparent echoed');
        $this->assertStringContainsString($context->traceId, $echoed);
        $this->assertStringNotContainsString($context->parentId, $echoed, 'must emit our span, not replay the caller\'s');
    }

    public function test_the_native_scheme_remains_available(): void
    {
        $result = (new RelayConformanceHarness)->triggerSignedDelivery('webhook-relay-v0');

        $this->assertStringStartsWith('t=', $result['headers']['x-webhook-signature']);
        $this->assertArrayNotHasKey('webhook-signature', $result['headers']);
    }
}
