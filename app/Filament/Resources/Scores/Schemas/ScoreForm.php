<?php

namespace App\Filament\Resources\Scores\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ScoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required(),
                Select::make('contestant_id')
                    ->relationship('contestant', 'name')
                    ->required(),
                Select::make('judge_id')
                    ->relationship('judge', 'name')
                    ->required(),
                Select::make('criteria_id')
                    ->relationship('criteria', 'name'),
                Select::make('round_id')
                    ->relationship('round', 'name'),
                TextInput::make('score')
                    ->required()
                    ->numeric(),
                Textarea::make('comments')
                    ->columnSpanFull(),
            ]);
    }
}
