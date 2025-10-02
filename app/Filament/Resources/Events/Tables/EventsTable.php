<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('judging_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'criteria' => 'success',
                        'rounds' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'criteria' => 'Criteria-based',
                        'rounds' => 'Rounds-based',
                    }),
                TextColumn::make('organizer.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('contestants_count')
                    ->counts('contestants')
                    ->label('Contestants'),
                TextColumn::make('judges_count')
                    ->counts('judges')
                    ->label('Judges'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
