<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RecentEvents extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $user = auth()->user();
        
        // Build query based on user permissions
        $query = Event::query()->latest()->limit(5);
        
        // Filter by organization for non-super-admins
        if (!$user->isSuperAdmin()) {
            $query->whereIn('organization_id', $user->accessibleOrganizationIds());
        }
        
        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->url(fn (Event $record) => route('filament.admin.resources.events.edit', ['record' => $record]))
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('Organization')
                    ->searchable()
                    ->default('—'),
                
                Tables\Columns\TextColumn::make('judging_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'criteria' => 'success',
                        'rounds' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'criteria' => 'Criteria-based',
                        'rounds' => 'Rounds-based',
                    }),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime('M j, Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('contestants_count')
                    ->counts('contestants')
                    ->label('Contestants')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('judges_count')
                    ->counts('judges')
                    ->label('Judges')
                    ->badge()
                    ->color('warning'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->heading('Recent Events');
    }
}
