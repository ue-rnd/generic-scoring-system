<?php

namespace App\Filament\Resources\Criterias;

use App\Filament\Resources\Criterias\Pages\CreateCriteria;
use App\Filament\Resources\Criterias\Pages\EditCriteria;
use App\Filament\Resources\Criterias\Pages\ListCriterias;
use App\Filament\Resources\Criterias\Schemas\CriteriaForm;
use App\Filament\Resources\Criterias\Tables\CriteriasTable;
use App\Models\Criteria;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CriteriaResource extends Resource
{
    protected static ?string $model = Criteria::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    
    protected static bool $shouldRegisterNavigation = false;
    
    /**
     * Scope the query to only show criteria from the user's organizations
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $user = auth()->user();
        
        // Super admins can see all criteria
        if ($user->isSuperAdmin()) {
            return $query;
        }
        
        // Other users can only see criteria from their organizations
        return $query->whereIn('organization_id', $user->accessibleOrganizationIds());
    }

    public static function form(Schema $schema): Schema
    {
        return CriteriaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CriteriasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCriterias::route('/'),
            'create' => CreateCriteria::route('/create'),
            'edit' => EditCriteria::route('/{record}/edit'),
        ];
    }
}
