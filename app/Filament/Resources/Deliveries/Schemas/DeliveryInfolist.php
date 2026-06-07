<?php

namespace App\Filament\Resources\Deliveries\Schemas;

use App\Models\Delivery;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeliveryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Delivery')
                    ->columns(4)
                    ->components([
                        TextEntry::make('id')->label('ID')->fontFamily('mono')->copyable(),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                Delivery::STATUS_SUCCESS => 'success',
                                Delivery::STATUS_PENDING => 'info',
                                Delivery::STATUS_FAILED => 'warning',
                                Delivery::STATUS_DEAD => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('attempts_made')->numeric(),
                        TextEntry::make('final_status_code')->label('Final HTTP')->placeholder('—'),
                        TextEntry::make('event.type')->label('Event type')->badge()->color('info'),
                        TextEntry::make('event.id')->label('Event ID')->fontFamily('mono')->copyable(),
                        TextEntry::make('subscription.url')->label('Target URL')->fontFamily('mono')->copyable()->columnSpan(2),
                        TextEntry::make('workspace.slug')->label('Workspace')->badge()->color('gray'),
                        TextEntry::make('next_attempt_at')->label('Next attempt')->dateTime()->placeholder('—'),
                        TextEntry::make('completed_at')->dateTime()->placeholder('—'),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
                Section::make('Attempt timeline')
                    ->components([
                        RepeatableEntry::make('attempts')
                            ->hiddenLabel()
                            ->columns(4)
                            ->schema([
                                TextEntry::make('attempt_number')->label('Attempt')->badge(),
                                TextEntry::make('response_status')
                                    ->label('HTTP')
                                    ->badge()
                                    ->color(fn (?int $code) => match (true) {
                                        $code === null => 'gray',
                                        $code >= 200 && $code < 300 => 'success',
                                        $code >= 400 && $code < 500 => 'warning',
                                        default => 'danger',
                                    })
                                    ->placeholder('—'),
                                TextEntry::make('latency_ms')->label('Latency (ms)')->numeric()->placeholder('—'),
                                TextEntry::make('attempted_at')->dateTime(),
                                TextEntry::make('error_code')->placeholder('—')->fontFamily('mono'),
                                TextEntry::make('request_signature')
                                    ->label('X-Webhook-Signature')
                                    ->fontFamily('mono')
                                    ->columnSpan(3),
                                TextEntry::make('response_body_snippet')
                                    ->label('Response body (first 4 KB)')
                                    ->fontFamily('mono')
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
