<?php

namespace App\Filament\Resources\Workspaces;

use App\Filament\Resources\Workspaces\Pages\ListWorkspaces;
use App\Filament\Resources\Workspaces\Pages\ViewWorkspace;
use App\Filament\Resources\Workspaces\Schemas\WorkspaceInfolist;
use App\Filament\Resources\Workspaces\Tables\WorkspacesTable;
use App\Models\Workspace;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkspaceResource extends Resource
{
    protected static ?string $model = Workspace::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 10;

    public static function infolist(Schema $schema): Schema
    {
        return WorkspaceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkspacesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkspaces::route('/'),
            'view' => ViewWorkspace::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
