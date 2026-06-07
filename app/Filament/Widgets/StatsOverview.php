<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use App\Models\Event;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $since24h = now()->subDay();
        $since7d = now()->subDays(7);

        $events24h = Event::where('created_at', '>=', $since24h)->count();
        $events7d = Event::where('created_at', '>=', $since7d)->count();

        $deliveries24h = Delivery::where('created_at', '>=', $since24h)->count();
        $succeeded24h = Delivery::where('created_at', '>=', $since24h)
            ->where('status', Delivery::STATUS_SUCCESS)
            ->count();

        $successRate = $deliveries24h === 0
            ? null
            : round(($succeeded24h / $deliveries24h) * 100, 1);

        $deadOpen = Delivery::where('status', Delivery::STATUS_DEAD)->count();

        $pausedSubs = Subscription::where('state', Subscription::STATE_PAUSED)->count();

        return [
            Stat::make('Events / 24h', number_format($events24h))
                ->description('Last 7 days: '.number_format($events7d))
                ->descriptionColor('gray')
                ->color('info'),

            Stat::make('Deliveries / 24h', number_format($deliveries24h))
                ->description('Succeeded: '.number_format($succeeded24h))
                ->descriptionColor('success')
                ->color('info'),

            Stat::make('Success rate (24h)', $successRate === null ? '—' : "{$successRate}%")
                ->description($successRate === null ? 'No deliveries yet' : ($succeeded24h.' / '.$deliveries24h))
                ->color($successRate === null ? 'gray' : ($successRate >= 99 ? 'success' : ($successRate >= 95 ? 'warning' : 'danger'))),

            Stat::make('Dead-letter queue', number_format($deadOpen))
                ->description($pausedSubs.' subscription(s) paused')
                ->color($deadOpen === 0 ? 'success' : 'danger'),
        ];
    }
}
