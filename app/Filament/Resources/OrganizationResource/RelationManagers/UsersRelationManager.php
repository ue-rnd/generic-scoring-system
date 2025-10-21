<?php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Organization Members';

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
                    ->sortable()
                    ->copyable(),
                
                TextColumn::make('pivot.role')
                    ->label('Role')
                    ->badge()
                    ->color(fn ($state) => $state === 'admin' ? 'success' : 'primary')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                
                IconColumn::make('is_super_admin')
                    ->label('Super Admin')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning'),
                
                TextColumn::make('pivot.created_at')
                    ->label('Added')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add Member')
                    ->preloadRecordSelect()
                    ->recordSelect(
                        fn (Select $select) => $select
                            ->label('User')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                    )
                    ->form([
                        Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'member' => 'Member',
                            ])
                            ->required()
                            ->default('member')
                            ->helperText('Admins can manage events and organization settings.'),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'member' => 'Member',
                            ])
                            ->required()
                            ->helperText('Admins can manage events and organization settings.'),
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
