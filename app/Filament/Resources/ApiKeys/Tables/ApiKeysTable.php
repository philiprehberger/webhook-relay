<?php

namespace App\Filament\Resources\ApiKeys\Tables;

use App\Models\ApiKey;
use App\Models\Workspace;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ApiKeysTable
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
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('prefix')
                    ->fontFamily('mono')
                    ->badge()
                    ->color(fn (string $state) => $state === 'whk_live_' ? 'success' : 'gray'),
                TextColumn::make('last_four')
                    ->label('Last 4')
                    ->fontFamily('mono')
                    ->formatStateUsing(fn (string $state) => "···{$state}"),
                TextColumn::make('last_used_at')
                    ->dateTime()
                    ->since()
                    ->placeholder('Never'),
                IconColumn::make('revoked_at')
                    ->label('Revoked?')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
                TextColumn::make('created_at')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('workspace_id')
                    ->label('Workspace')
                    ->options(fn () => Workspace::pluck('slug', 'id')->all()),
                TernaryFilter::make('revoked_at')
                    ->label('Revoked')
                    ->placeholder('All')
                    ->trueLabel('Revoked only')
                    ->falseLabel('Active only')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('revoked_at'),
                        false: fn ($query) => $query->whereNull('revoked_at'),
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Action::make('mint')
                    ->label('Mint key')
                    ->icon('heroicon-o-key')
                    ->color('success')
                    ->schema([
                        \Filament\Forms\Components\Select::make('workspace_id')
                            ->label('Workspace')
                            ->options(fn () => Workspace::orderBy('slug')->pluck('slug', 'id')->all())
                            ->required()
                            ->searchable(),
                        \Filament\Forms\Components\TextInput::make('name')
                            ->placeholder('CI ingest key')
                            ->maxLength(255),
                        \Filament\Forms\Components\Select::make('env')
                            ->options(['live' => 'live (whk_live_)', 'test' => 'test (whk_test_)'])
                            ->required()
                            ->default('test'),
                    ])
                    ->action(function (array $data) {
                        $workspace = Workspace::findOrFail($data['workspace_id']);
                        [$apiKey, $plaintext] = ApiKey::mint($workspace, $data['env'], $data['name'] ?? null);

                        Notification::make()
                            ->title('Key minted')
                            ->body('Plaintext (record now, only shown once):  '.$plaintext)
                            ->success()
                            ->persistent()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('revoke')
                    ->label('Revoke')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (ApiKey $record) => $record->revoked_at === null)
                    ->action(function (ApiKey $record) {
                        $record->update(['revoked_at' => now()]);
                        Notification::make()->title('Key revoked')->success()->send();
                    }),
            ]);
    }
}
