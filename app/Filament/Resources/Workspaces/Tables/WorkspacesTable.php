<?php

namespace App\Filament\Resources\Workspaces\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkspacesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('medium'),
                TextColumn::make('slug')->color('gray')->copyable(),
                TextColumn::make('api_keys_count')
                    ->counts('apiKeys')
                    ->label('API keys')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('subscriptions_count')
                    ->counts('subscriptions')
                    ->label('Subscriptions')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('events_count')
                    ->counts('events')
                    ->label('Events')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('created_at')->date()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
