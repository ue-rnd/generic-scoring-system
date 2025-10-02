<?php

namespace App\Filament\Resources\Criterias\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CriteriaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('weight')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('max_score')
                    ->required()
                    ->numeric(),
                TextInput::make('min_score')
                    ->required()
                    ->numeric()
                    ->default(0),
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
