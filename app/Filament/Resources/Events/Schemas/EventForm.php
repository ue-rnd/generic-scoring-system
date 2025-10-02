<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
                Select::make('judging_type')
                    ->options([
                        'criteria' => 'Criteria-based (e.g., Beauty Pageants)',
                        'rounds' => 'Rounds-based (e.g., Quiz Bees)',
                    ])
                    ->required()
                    ->native(false),
                Select::make('organizer_id')
                    ->relationship('organizer', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('start_date')
                    ->required()
                    ->native(false),
                DateTimePicker::make('end_date')
                    ->required()
                    ->native(false)
                    ->after('start_date'),
                Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),
            ]);
    }
}
