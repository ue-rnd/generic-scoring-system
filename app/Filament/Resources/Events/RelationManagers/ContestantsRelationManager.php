<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\Contestants\ContestantResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContestantsRelationManager extends RelationManager
{
    protected static string $relationship = 'contestants';

    protected static ?string $relatedResource = ContestantResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                
                TextColumn::make('phone')
                    ->searchable(),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                
                TextColumn::make('scores_count')
                    ->counts('scores')
                    ->label('Scores')
                    ->badge()
                    ->color('info'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['organization_id'] = $this->getOwnerRecord()->organization_id;
                        return $data;
                    })
                    ->form([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Toggle::make('is_active')
                            ->default(true),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
