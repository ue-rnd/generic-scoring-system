<?php

namespace App\Filament\Resources\Rounds\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoundForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('total_questions')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('points_per_question')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('max_score')
                    ->required()
                    ->numeric(),
                Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required(),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
