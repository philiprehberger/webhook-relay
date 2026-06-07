<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class FailingSubscriptions extends TableWidget
{
    protected static ?string $heading = 'Subscriptions on the breaker';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Subscription::query()
                ->where('consecutive_failures', '>', 0)
                ->orderByDesc('consecutive_failures')
                ->limit(10))
            ->columns([
                TextColumn::make('workspace.slug')
                    ->label('Workspace')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('name')->placeholder('—'),
                TextColumn::make('url')->fontFamily('mono')->limit(48),
                TextColumn::make('state')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        Subscription::STATE_ACTIVE => 'success',
                        Subscription::STATE_PAUSED => 'warning',
                        Subscription::STATE_DISABLED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('consecutive_failures')
                    ->label('Failure streak')
                    ->numeric()
                    ->alignCenter()
                    ->color(fn (int $state) => $state >= 5 ? 'danger' : 'warning')
                    ->sortable(),
                TextColumn::make('paused_at')
                    ->dateTime()
                    ->since()
                    ->placeholder('—'),
            ])
            ->paginated(false);
    }
}
