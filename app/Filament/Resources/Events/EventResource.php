<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages;
use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Events\Pages\ManageEventAccess;
use App\Filament\Resources\Events\RelationManagers\ContestantsRelationManager;
use App\Filament\Resources\Events\RelationManagers\CriteriasRelationManager;
use App\Filament\Resources\Events\RelationManagers\JudgesRelationManager;
use App\Filament\Resources\Events\RelationManagers\RoundsRelationManager;
use App\Filament\Resources\Events\Schemas\EventForm;
use App\Filament\Resources\Events\Tables\EventsTable;
use App\Models\Event;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    
    protected static ?string $navigationLabel = 'Events';
    
    protected static ?string $modelLabel = 'Event';
    
    protected static ?string $pluralModelLabel = 'Events';
    

    public static function form(Schema $schema): Schema
    {
        return EventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ContestantsRelationManager::class,
            JudgesRelationManager::class,
            CriteriasRelationManager::class,
            RoundsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit' => EditEvent::route('/{record}/edit'),
            'manage-access' => ManageEventAccess::route('/{record}/manage-access'),
            'score-quiz-bee' => Pages\ScoreQuizBee::route('/{record}/score-quiz-bee'),
        ];
    }
}
