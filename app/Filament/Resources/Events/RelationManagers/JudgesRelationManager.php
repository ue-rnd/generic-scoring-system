<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\Judges\JudgeResource;
use App\Models\Judge;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JudgesRelationManager extends RelationManager
{
    protected static string $relationship = 'judges';

    protected static ?string $relatedResource = JudgeResource::class;

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
                
                TextColumn::make('specialization')
                    ->searchable(),
                
                TextColumn::make('pivot.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'accepted' => 'success',
                        'pending' => 'warning',
                        'declined' => 'danger',
                        default => 'gray'
                    }),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add Existing Judge')
                    ->preloadRecordSelect()
                    ->recordSelect(
                        fn (Select $select) => $select
                            ->label('Judge')
                            ->options(
                                Judge::where('organization_id', $this->getOwnerRecord()->organization_id)
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                    )
                    ->form([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'accepted' => 'Accepted',
                                'declined' => 'Declined',
                            ])
                            ->default('accepted'),
                    ]),
                    
                CreateAction::make()
                    ->label('Create New Judge')
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
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        
                        TextInput::make('specialization')
                            ->maxLength(255),
                        
                        Textarea::make('bio')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Toggle::make('is_active')
                            ->default(true),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'accepted' => 'Accepted',
                                'declined' => 'Declined',
                            ]),
                    ]),
                DetachAction::make()
                    ->label('Remove'),
            ])
            ->toolbarActions([
                DetachBulkAction::make()
                    ->label('Remove Selected'),
            ]);
    }
}
