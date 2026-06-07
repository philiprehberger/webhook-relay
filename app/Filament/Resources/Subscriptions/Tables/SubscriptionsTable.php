<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use App\Models\Subscription;
use App\Models\Workspace;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('workspace.slug')
                    ->label('Workspace')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('name')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('url')
                    ->fontFamily('mono')
                    ->limit(48)
                    ->copyable(),
                TextColumn::make('event_filter')
                    ->badge()
                    ->color('info'),
                TextColumn::make('state')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        Subscription::STATE_ACTIVE => 'success',
                        Subscription::STATE_PAUSED => 'warning',
                        Subscription::STATE_DISABLED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('consecutive_failures')
                    ->label('Streak')
                    ->numeric()
                    ->alignCenter()
                    ->color(fn (int $state) => $state >= 5 ? 'danger' : ($state > 0 ? 'warning' : 'gray'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->options([
                        Subscription::STATE_ACTIVE => 'Active',
                        Subscription::STATE_PAUSED => 'Paused',
                        Subscription::STATE_DISABLED => 'Disabled',
                    ]),
                SelectFilter::make('workspace_id')
                    ->label('Workspace')
                    ->options(fn () => Workspace::pluck('slug', 'id')->all()),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
