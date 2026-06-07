<?php

namespace App\Filament\Resources\Deliveries\Pages;

use App\Filament\Resources\Deliveries\DeliveryResource;
use App\Jobs\DeliverEventToSubscription;
use App\Models\Delivery;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewDelivery extends ViewRecord
{
    protected static string $resource = DeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retry')
                ->label('Retry now')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn (Delivery $record) => $record->status !== Delivery::STATUS_SUCCESS)
                ->action(function (Delivery $record) {
                    $record->update([
                        'status' => Delivery::STATUS_PENDING,
                        'next_attempt_at' => null,
                        'completed_at' => null,
                    ]);
                    DeliverEventToSubscription::dispatch($record->event_id, $record->subscription_id);
                    Notification::make()->title('Delivery requeued')->success()->send();
                }),
        ];
    }
}
