<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Filament\Resources\OrganizationResource\RelationManagers\UsersRelationManager;
use App\Filament\Resources\OrganizationResource\Schemas\OrganizationForm;
use App\Filament\Resources\OrganizationResource\Tables\OrganizationsTable;
use App\Models\Organization;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static UnitEnum|string|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return OrganizationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'view' => Pages\ViewOrganization::route('/{record}'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Super admins can see all organizations
        if (Auth::check() && Auth::user()->isSuperAdmin()) {
            return $query;
        }

        // Regular users can only see organizations they belong to
        if (Auth::check()) {
            return $query->whereHas('users', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        return Auth::check();
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->isSuperAdmin();
    }

    public static function canEdit($record): bool
    {
        if (Auth::user()->isSuperAdmin()) {
            return true;
        }

        return Auth::user()->isAdminOfOrganization($record);
    }

    public static function canDelete($record): bool
    {
        return Auth::user()->isSuperAdmin();
    }
}
