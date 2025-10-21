<?php

namespace App\Filament\Resources\OrganizationResource\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                
                FileUpload::make('logo')
                    ->image()
                    ->directory('organization-logos')
                    ->imageEditor()
                    ->columnSpanFull(),
                
                Select::make('head_user_id')
                    ->label('Organization Head')
                    ->options(function () {
                        if (Auth::user()->isSuperAdmin()) {
                            return User::pluck('name', 'id');
                        }
                        // For org admins, show only users in their organizations
                        return User::whereHas('organizations', function ($query) {
                            $query->whereIn('organization_id', Auth::user()->accessibleOrganizationIds());
                        })->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->helperText('The organization head has full admin rights over this organization'),
                
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Inactive organizations cannot create new events'),
            ]);
    }
}
