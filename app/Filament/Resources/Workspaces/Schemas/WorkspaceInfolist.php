<?php

namespace App\Filament\Resources\Workspaces\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WorkspaceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Workspace')
                    ->columns(2)
                    ->components([
                        TextEntry::make('id')->label('ID')->copyable(),
                        TextEntry::make('slug')->copyable(),
                        TextEntry::make('name'),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
                Section::make('Counts')
                    ->columns(4)
                    ->components([
                        TextEntry::make('api_keys_count')
                            ->state(fn ($record) => $record->apiKeys()->count())
                            ->label('API keys')
                            ->numeric(),
                        TextEntry::make('subscriptions_count')
                            ->state(fn ($record) => $record->subscriptions()->count())
                            ->label('Subscriptions')
                            ->numeric(),
                        TextEntry::make('events_count')
                            ->state(fn ($record) => $record->events()->count())
                            ->label('Events')
                            ->numeric(),
                        TextEntry::make('idempotency_records_count')
                            ->state(fn ($record) => $record->idempotencyRecords()->count())
                            ->label('Idempotency records')
                            ->numeric(),
                    ]),
            ]);
    }
}
