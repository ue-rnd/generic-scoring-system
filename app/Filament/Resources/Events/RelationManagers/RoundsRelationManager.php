<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\Rounds\RoundResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoundsRelationManager extends RelationManager
{
    protected static string $relationship = 'rounds';

    protected static ?string $relatedResource = RoundResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('order'))
            ->columns([
                TextColumn::make('order')
                    ->label('#')
                    ->sortable(),
                
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('total_questions')
                    ->label('Questions')
                    ->sortable(),
                
                TextColumn::make('points_per_question')
                    ->label('Pts/Question')
                    ->sortable(),
                
                TextColumn::make('max_score')
                    ->label('Max Score')
                    ->sortable(),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
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
                        
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        TextInput::make('total_questions')
                            ->numeric()
                            ->required()
                            ->default(10)
                            ->helperText('Number of questions in this round'),
                        
                        TextInput::make('points_per_question')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->helperText('Points awarded for each correct answer'),
                        
                        TextInput::make('max_score')
                            ->numeric()
                            ->default(10)
                            ->helperText('Maximum possible score for this round'),
                        
                        TextInput::make('order')
                            ->numeric()
                            ->default(1),
                        
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
