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

class ContestantResource extends Resource
{
    protected static ?string $model = Contestant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    
    protected static ?string $navigationLabel = 'Contestants';
    
    protected static ?string $modelLabel = 'Contestant';
    
    protected static ?string $pluralModelLabel = 'Contestants';
    

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
