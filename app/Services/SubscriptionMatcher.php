<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Subscription;
use Illuminate\Support\Collection;

/**
 * Resolves which subscriptions should receive a given event.
 *
 * Filter syntax:
 *   "*"              -> match all events
 *   "order.created"  -> exact match
 *   "order.*"        -> prefix glob ("order.X", "order.X.Y" — "*" is a
 *                       segment wildcard, NOT a "starts with" prefix)
 *
 * Only `active` subscriptions match; paused / disabled are skipped.
 */
class SubscriptionMatcher
{
    /**
     * @return Collection<int, Subscription>
     */
    public function matchingSubscriptions(Event $event): Collection
    {
        return Subscription::query()
            ->where('workspace_id', $event->workspace_id)
            ->where('state', Subscription::STATE_ACTIVE)
            ->get()
            ->filter(fn (Subscription $s) => $s->matches($event->type))
            ->values();
    }
}
