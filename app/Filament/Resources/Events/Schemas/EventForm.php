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
                // Basic Information
                TextInput::make('name')
                    ->label('Event Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                
                Textarea::make('description')
                    ->label('Description')
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
                    ->label('Active')
                    ->default(true),
                
                // Event Schedule
                DateTimePicker::make('start_date')
                    ->label('Start Date')
                    ->required()
                    ->native(false),
                
                DateTimePicker::make('end_date')
                    ->label('End Date')
                    ->required()
                    ->native(false)
                    ->after('start_date'),
                
                // Event Type & Scoring Configuration
                Select::make('judging_type')
                    ->label('Judging Type')
                    ->options([
                        'criteria' => 'Criteria-based (e.g., Beauty Pageants)',
                        'rounds' => 'Rounds-based (e.g., Quiz Bees)',
                    ])
                    ->required()
                    ->live()
                    ->native(false)
                    ->helperText('Criteria-based: Judges score contestants on multiple criteria. Rounds-based: Contestants compete in multiple rounds.')
                    ->columnSpanFull(),
                
                Select::make('scoring_mode')
                    ->label('Scoring Mode (Quiz Bee Only)')
                    ->options([
                        'manual' => 'Manual Score Entry',
                        'boolean' => 'Correct/Incorrect (Auto-calculate)',
                    ])
                    ->default('manual')
                    ->required()
                    ->native(false)
                    ->visible(fn ($get) => $get('judging_type') === 'rounds')
                    ->helperText('Boolean: Mark answers as correct/incorrect. Manual: Enter scores directly.')
                    ->columnSpanFull(),
                
                // Public Viewing Options
                Toggle::make('public_viewing_config.show_rankings')
                    ->label('Public: Show Rankings')
                    ->default(true)
                    ->live()
                    ->helperText('Display contestant rankings on public scoreboard'),
                
                Toggle::make('public_viewing_config.show_scores')
                    ->label('Public: Show Final Scores')
                    ->default(false)
                    ->live()
                    ->helperText('Display the actual score values'),
                
                Toggle::make('public_viewing_config.show_judge_names')
                    ->label('Public: Show Judge Names (Pageant)')
                    ->default(false)
                    ->live()
                    ->visible(fn ($get) => $get('judging_type') === 'criteria')
                    ->helperText('Display names of judges'),
                
                Toggle::make('public_viewing_config.show_individual_scores')
                    ->label('Public: Show Individual Judge Scores (Pageant)')
                    ->default(false)
                    ->live()
                    ->visible(fn ($get) => $get('judging_type') === 'criteria')
                    ->helperText('Display scores from each judge separately'),
                
                Toggle::make('public_viewing_config.show_criteria_breakdown')
                    ->label('Public: Show Criteria Breakdown (Pageant)')
                    ->default(false)
                    ->live()
                    ->visible(fn ($get) => $get('judging_type') === 'criteria')
                    ->helperText('Display scores for each criterion'),
                
                Toggle::make('public_viewing_config.show_round_breakdown')
                    ->label('Public: Show Round Breakdown (Quiz Bee)')
                    ->default(false)
                    ->live()
                    ->visible(fn ($get) => $get('judging_type') === 'rounds')
                    ->helperText('Display scores for each round'),
                
                Toggle::make('public_viewing_config.show_judge_progress')
                    ->label('Public: Show Judge Progress (Pageant)')
                    ->default(true)
                    ->live()
                    ->visible(fn ($get) => $get('judging_type') === 'criteria')
                    ->helperText('Display completion status of each judge'),
            ])
            ->columns(2);
    }
}
