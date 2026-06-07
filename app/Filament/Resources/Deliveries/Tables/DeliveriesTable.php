<?php

namespace App\Filament\Resources\Deliveries\Tables;

use App\Jobs\DeliverEventToSubscription;
use App\Models\Delivery;
use App\Models\Workspace;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DeliveriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        Delivery::STATUS_SUCCESS => 'success',
                        Delivery::STATUS_PENDING => 'info',
                        Delivery::STATUS_FAILED => 'warning',
                        Delivery::STATUS_DEAD => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('event.type')
                    ->label('Event type')
                    ->badge()
                    ->color('info'),
                TextColumn::make('subscription.url')
                    ->label('Target')
                    ->limit(40)
                    ->tooltip(fn (Delivery $record) => $record->subscription?->url)
                    ->fontFamily('mono'),
                TextColumn::make('attempts_made')
                    ->label('Attempts')
                    ->numeric()
                    ->alignCenter(),
                TextColumn::make('final_status_code')
                    ->label('HTTP')
                    ->alignCenter()
                    ->placeholder('—')
                    ->color(fn (?int $code) => match (true) {
                        $code === null => 'gray',
                        $code >= 200 && $code < 300 => 'success',
                        $code >= 400 && $code < 500 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('next_attempt_at')
                    ->label('Next attempt')
                    ->dateTime()
                    ->since()
                    ->placeholder('—'),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->since()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('workspace.slug')
                    ->label('Workspace')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Delivery::STATUS_PENDING => 'Pending',
                        Delivery::STATUS_SUCCESS => 'Success',
                        Delivery::STATUS_FAILED => 'Failed',
                        Delivery::STATUS_DEAD => 'Dead',
                    ]),
                SelectFilter::make('workspace_id')
                    ->label('Workspace')
                    ->options(fn () => Workspace::pluck('slug', 'id')->all()),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Delivery $record) => $record->status !== Delivery::STATUS_SUCCESS)
                    ->requiresConfirmation()
                    ->action(function (Delivery $record) {
                        $record->update([
                            'status' => Delivery::STATUS_PENDING,
                            'next_attempt_at' => null,
                            'completed_at' => null,
                        ]);
                        DeliverEventToSubscription::dispatch($record->event_id, $record->subscription_id);
                        Notification::make()->title('Delivery requeued')->success()->send();
                    }),
            ]);
    }
}
