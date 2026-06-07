<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscription')
                    ->columns(3)
                    ->components([
                        TextEntry::make('id')->label('ID')->fontFamily('mono')->copyable(),
                        TextEntry::make('workspace.slug')->label('Workspace')->badge()->color('gray'),
                        TextEntry::make('state')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'active' => 'success',
                                'paused' => 'warning',
                                'disabled' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('name')->placeholder('—'),
                        TextEntry::make('event_filter')->badge()->color('info'),
                        TextEntry::make('consecutive_failures')->label('Failure streak')->numeric(),
                        TextEntry::make('url')->fontFamily('mono')->copyable()->columnSpanFull(),
                    ]),
                Section::make('Lifecycle')
                    ->columns(3)
                    ->components([
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('paused_at')->dateTime()->placeholder('—'),
                        TextEntry::make('secret_rotated_at')->dateTime()->placeholder('—'),
                    ]),
            ]);
    }
}
