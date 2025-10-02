<?php

namespace App\Filament\Resources\Contestants\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContestantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
                Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),
            ]);
    }
}
