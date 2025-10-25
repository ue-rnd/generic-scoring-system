<?php

namespace App\Filament\Resources\Contestants;

use App\Filament\Resources\Contestants\Pages\CreateContestant;
use App\Filament\Resources\Contestants\Pages\EditContestant;
use App\Filament\Resources\Contestants\Pages\ListContestants;
use App\Filament\Resources\Contestants\Schemas\ContestantForm;
use App\Filament\Resources\Contestants\Tables\ContestantsTable;
use App\Models\Contestant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContestantResource extends Resource
{
    protected static ?string $model = Contestant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    
    protected static ?string $navigationLabel = 'Contestants';
    
    protected static ?string $modelLabel = 'Contestant';
    
    protected static ?string $pluralModelLabel = 'Contestants';
    
    protected static bool $shouldRegisterNavigation = false;
    
    /**
     * Scope the query to only show contestants from the user's organizations
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $user = auth()->user();
        
        // Super admins can see all contestants
        if ($user->isSuperAdmin()) {
            return $query;
        }
        
        // Other users can only see contestants from their organizations
        return $query->whereIn('organization_id', $user->accessibleOrganizationIds());
    }

    public static function form(Schema $schema): Schema
    {
        return ContestantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContestantsTable::configure($table);
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
            'index' => ListContestants::route('/'),
            'create' => CreateContestant::route('/create'),
            'edit' => EditContestant::route('/{record}/edit'),
        ];
    }
}
