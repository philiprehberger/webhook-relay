<?php

namespace App\Filament\Resources\Events\Tables;

use App\Models\Workspace;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->fontFamily('mono')
                    ->copyable()
                    ->limit(12),
                TextColumn::make('workspace.slug')
                    ->label('Workspace')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('type')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('deliveries_count')
                    ->counts('deliveries')
                    ->label('Deliveries')
                    ->alignCenter(),
                TextColumn::make('idempotency_key')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->fontFamily('mono')
                    ->limit(16),
                TextColumn::make('source_ip')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('workspace_id')
                    ->label('Workspace')
                    ->options(fn () => Workspace::pluck('slug', 'id')->all()),
                Filter::make('type_starts_with')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('value')
                            ->label('Type starts with')
                            ->placeholder('order.'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['value'])) {
                            $query->where('type', 'like', $data['value'].'%');
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
