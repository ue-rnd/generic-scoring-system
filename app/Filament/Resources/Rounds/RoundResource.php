<?php

namespace App\Filament\Resources\Rounds;

use App\Filament\Resources\Rounds\Pages\CreateRound;
use App\Filament\Resources\Rounds\Pages\EditRound;
use App\Filament\Resources\Rounds\Pages\ListRounds;
use App\Filament\Resources\Rounds\Schemas\RoundForm;
use App\Filament\Resources\Rounds\Tables\RoundsTable;
use App\Models\Round;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoundResource extends Resource
{
    protected static ?string $model = Round::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;
    
    protected static ?string $navigationLabel = 'Rounds';
    
    protected static ?string $modelLabel = 'Round';
    
    protected static ?string $pluralModelLabel = 'Rounds';
    
    protected static bool $shouldRegisterNavigation = false;
    
    /**
     * Scope the query to only show rounds from the user's organizations
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $user = auth()->user();
        
        // Super admins can see all rounds
        if ($user->isSuperAdmin()) {
            return $query;
        }
        
        // Other users can only see rounds from their organizations
        return $query->whereIn('organization_id', $user->accessibleOrganizationIds());
    }

    public static function form(Schema $schema): Schema
    {
        return RoundForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoundsTable::configure($table);
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
            'index' => ListRounds::route('/'),
            'create' => CreateRound::route('/create'),
            'edit' => EditRound::route('/{record}/edit'),
        ];
    }
}
