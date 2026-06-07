<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class EventsChart extends ChartWidget
{
    protected ?string $heading = 'Events ingested — last 7 days';

    protected ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $start = now()->subDays(6)->startOfDay();

        $rows = Event::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as n')
            ->groupBy('day')
            ->pluck('n', 'day');

        $labels = [];
        $data = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $labels[] = $date->format('M j');
            $data[] = (int) ($rows[$date->format('Y-m-d')] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Events',
                    'data' => $data,
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.15)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
