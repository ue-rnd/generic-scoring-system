<?php

namespace App\Filament\Widgets;

use App\Models\Organization;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class MyOrganizations extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        $user = Auth::user();

        // Super admins see all organizations
        if ($user->isSuperAdmin()) {
            $query = Organization::query();
        } else {
            // Regular users see only their organizations
            $query = Organization::whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $table
            ->query($query->latest())
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-org.png')),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->url(fn (Organization $record) => route('filament.admin.resources.organizations.view', ['record' => $record]))
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Members')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('events_count')
                    ->counts('events')
                    ->label('Events')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('head.name')
                    ->label('Organization Head')
                    ->default('—'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->heading(Auth::user()->isSuperAdmin() ? 'All Organizations' : 'My Organizations');
    }
}
