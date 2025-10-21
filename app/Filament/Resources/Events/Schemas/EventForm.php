<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Models\Organization;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class EventForm
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
                    ->columnSpanFull()
                    ->rows(3),
                
                Select::make('organization_id')
                    ->label('Organization')
                    ->options(function () {
                        if (Auth::user()->isSuperAdmin()) {
                            return Organization::where('is_active', true)->pluck('name', 'id');
                        }
                        // Regular users can only select from their organizations
                        return Organization::whereHas('users', function ($query) {
                            $query->where('user_id', Auth::id());
                        })->where('is_active', true)->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Select the organization that owns this event'),
                
                Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),
                
                DateTimePicker::make('start_date')
                    ->required()
                    ->native(false),
                
                DateTimePicker::make('end_date')
                    ->required()
                    ->native(false)
                    ->after('start_date'),
                
                Select::make('judging_type')
                    ->options([
                        'criteria' => 'Criteria-based (e.g., Beauty Pageants)',
                        'rounds' => 'Rounds-based (e.g., Quiz Bees)',
                    ])
                    ->required()
                    ->reactive()
                    ->native(false)
                    ->helperText('Criteria-based: Judges score contestants on multiple criteria. Rounds-based: Contestants compete in multiple rounds/categories.')
                    ->columnSpanFull(),
                
                Select::make('scoring_mode')
                    ->options([
                        'manual' => 'Manual Score Entry',
                        'boolean' => 'Correct/Incorrect (Auto-calculate)',
                    ])
                    ->default('manual')
                    ->required()
                    ->native(false)
                    ->visible(fn ($get) => $get('judging_type') === 'rounds')
                    ->helperText('Boolean mode: Mark answers as correct/incorrect and points are auto-calculated. Manual mode: Enter scores directly.')
                    ->columnSpanFull(),
                
                // Public Viewing Settings
                Toggle::make('public_viewing_config.show_rankings')
                    ->label('Public: Show Rankings')
                    ->default(true)
                    ->helperText('Display contestant rankings on public page'),
                
                Toggle::make('public_viewing_config.show_scores')
                    ->label('Public: Show Final Scores')
                    ->default(false),
                
                Toggle::make('public_viewing_config.show_judge_names')
                    ->label('Public: Show Judge Names')
                    ->default(false),
                
                Toggle::make('public_viewing_config.show_individual_scores')
                    ->label('Public: Show Individual Judge Scores')
                    ->default(false),
                
                Toggle::make('public_viewing_config.show_criteria_breakdown')
                    ->label('Public: Show Criteria Breakdown')
                    ->default(false)
                    ->visible(fn ($get) => $get('judging_type') === 'criteria'),
                
                Toggle::make('public_viewing_config.show_round_breakdown')
                    ->label('Public: Show Round Breakdown')
                    ->default(false)
                    ->visible(fn ($get) => $get('judging_type') === 'rounds'),
                
                Toggle::make('public_viewing_config.show_judge_progress')
                    ->label('Public: Show Judge Progress')
                    ->default(true),
            ])
            ->columns(2);
    }
}


