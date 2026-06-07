<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Resources\Subscriptions\SubscriptionResource;
use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscription extends ViewRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            Action::make('pause')
                ->label('Pause')
                ->icon('heroicon-o-pause')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn (Subscription $record) => $record->isActive())
                ->action(function (Subscription $record) {
                    $record->pause();
                    Notification::make()->title('Subscription paused')->success()->send();
                }),

            Action::make('resume')
                ->label('Resume')
                ->icon('heroicon-o-play')
                ->color('success')
                ->visible(fn (Subscription $record) => $record->state === Subscription::STATE_PAUSED)
                ->action(function (Subscription $record) {
                    $record->resume();
                    Notification::make()->title('Subscription resumed, failure streak reset')->success()->send();
                }),

            Action::make('rotateSecret')
                ->label('Rotate secret')
                ->icon('heroicon-o-key')
                ->color('info')
                ->requiresConfirmation()
                ->modalDescription('A new signing secret will be generated. The previous one stays valid for 48 hours. The plaintext will be shown only once.')
                ->action(function (Subscription $record) {
                    $newSecret = $record->rotateSecret();
                    Notification::make()
                        ->title('Secret rotated')
                        ->body('New secret: '.$newSecret)
                        ->success()
                        ->persistent()
                        ->send();
                }),
        ];
    }
}
