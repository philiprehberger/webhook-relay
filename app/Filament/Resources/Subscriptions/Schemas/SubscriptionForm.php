<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\Subscription;
use App\Models\Workspace;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscription')
                    ->columns(2)
                    ->components([
                        Select::make('workspace_id')
                            ->label('Workspace')
                            ->options(fn () => Workspace::orderBy('slug')->pluck('slug', 'id')->all())
                            ->required()
                            ->disabledOn('edit')
                            ->searchable(),
                        TextInput::make('name')
                            ->maxLength(255)
                            ->placeholder('orders inbound'),
                        TextInput::make('url')
                            ->label('Delivery URL')
                            ->url()
                            ->required()
                            ->maxLength(2048)
                            ->prefix('https://')
                            ->dehydrateStateUsing(fn ($state) => self::ensureHttps($state))
                            ->columnSpanFull(),
                        TextInput::make('event_filter')
                            ->label('Event filter')
                            ->default('*')
                            ->required()
                            ->maxLength(128)
                            ->helperText('"*" matches everything. Use "order.created" for exact, or "order.*" for a glob.'),
                        Select::make('state')
                            ->options([
                                Subscription::STATE_ACTIVE => 'Active',
                                Subscription::STATE_PAUSED => 'Paused',
                                Subscription::STATE_DISABLED => 'Disabled',
                            ])
                            ->default(Subscription::STATE_ACTIVE)
                            ->required(),
                    ]),
                Section::make('Signing secret')
                    ->components([
                        TextInput::make('signing_secret')
                            ->label('Secret')
                            ->password()
                            ->revealable()
                            ->required(fn (string $context) => $context === 'create')
                            ->dehydrateStateUsing(fn ($state) => $state ?: Subscription::generateSecret())
                            ->default(fn () => Subscription::generateSecret())
                            ->helperText('Generated automatically on create. Rotate from the View page.'),
                    ])
                    ->visibleOn('create'),
            ]);
    }

    private static function ensureHttps(?string $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return $url;
        }
        if (str_starts_with($url, 'http://')) {
            return 'https://'.substr($url, 7);
        }
        if (! str_starts_with($url, 'https://')) {
            return 'https://'.$url;
        }

        return $url;
    }
}
