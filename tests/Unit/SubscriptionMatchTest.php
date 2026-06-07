<?php

namespace Tests\Unit;

use App\Models\Subscription;
use Tests\TestCase;

class SubscriptionMatchTest extends TestCase
{
    public function test_wildcard_matches_everything(): void
    {
        $sub = $this->make(filter: '*');

        $this->assertTrue($sub->matches('order.created'));
        $this->assertTrue($sub->matches('user.signed_up'));
        $this->assertTrue($sub->matches('weird.deeply.nested.type'));
    }

    public function test_exact_filter_only_matches_exact(): void
    {
        $sub = $this->make(filter: 'order.created');

        $this->assertTrue($sub->matches('order.created'));
        $this->assertFalse($sub->matches('order.shipped'));
        $this->assertFalse($sub->matches('order'));
        $this->assertFalse($sub->matches('order.created.bonus'));
    }

    public function test_glob_filter_matches_suffix(): void
    {
        $sub = $this->make(filter: 'order.*');

        $this->assertTrue($sub->matches('order.created'));
        $this->assertTrue($sub->matches('order.shipped'));
        $this->assertTrue($sub->matches('order.deeply.nested'));
        $this->assertFalse($sub->matches('user.created'));
    }

    public function test_paused_subscription_never_matches(): void
    {
        $sub = $this->make(filter: '*', state: Subscription::STATE_PAUSED);

        $this->assertFalse($sub->matches('anything'));
    }

    public function test_disabled_subscription_never_matches(): void
    {
        $sub = $this->make(filter: '*', state: Subscription::STATE_DISABLED);

        $this->assertFalse($sub->matches('anything'));
    }

    private function make(string $filter, string $state = Subscription::STATE_ACTIVE): Subscription
    {
        $s = new Subscription();
        $s->event_filter = $filter;
        $s->state = $state;

        return $s;
    }
}
