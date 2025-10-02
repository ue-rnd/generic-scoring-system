<?php

namespace App\Filament\Resources\Judges;

use App\Filament\Resources\Judges\Pages\CreateJudge;
use App\Filament\Resources\Judges\Pages\EditJudge;
use App\Filament\Resources\Judges\Pages\ListJudges;
use App\Filament\Resources\Judges\Schemas\JudgeForm;
use App\Filament\Resources\Judges\Tables\JudgesTable;
use App\Models\Judge;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JudgeResource extends Resource
{
    protected static ?string $model = Judge::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;
    
    protected static ?string $navigationLabel = 'Judges';
    
    protected static ?string $modelLabel = 'Judge';
    
    protected static ?string $pluralModelLabel = 'Judges';
    

    public static function form(Schema $schema): Schema
    {
        return JudgeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JudgesTable::configure($table);
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
            'index' => ListJudges::route('/'),
            'create' => CreateJudge::route('/create'),
            'edit' => EditJudge::route('/{record}/edit'),
        ];
    }
}
