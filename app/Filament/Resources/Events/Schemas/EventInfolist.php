<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event')
                    ->columns(3)
                    ->components([
                        TextEntry::make('id')->label('ID')->fontFamily('mono')->copyable(),
                        TextEntry::make('type')->badge()->color('info'),
                        TextEntry::make('workspace.slug')->label('Workspace')->badge()->color('gray'),
                        TextEntry::make('idempotency_key')->fontFamily('mono')->placeholder('—'),
                        TextEntry::make('source_ip')->placeholder('—'),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
                Section::make('Payload')
                    ->components([
                        TextEntry::make('payload')
                            ->hiddenLabel()
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                            ->fontFamily('mono')
                            ->columnSpanFull(),
                    ]),
                Section::make('Deliveries')
                    ->components([
                        TextEntry::make('deliveries_summary')
                            ->hiddenLabel()
                            ->state(fn ($record) => $record->deliveriesSummary())
                            ->formatStateUsing(fn (array $s) => sprintf(
                                'total %d  ·  succeeded %d  ·  failed %d  ·  pending %d',
                                $s['total'], $s['succeeded'], $s['failed'], $s['pending'],
                            ))
                            ->fontFamily('mono')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
