<?php

namespace App\Filament\Widgets;

use App\Models\Contestant;
use App\Models\Event;
use App\Models\Judge;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Super admins see all stats
        if ($user->isSuperAdmin()) {
            return [
                Stat::make('Total Events', Event::count())
                    ->description('All events in the system')
                    ->descriptionIcon('heroicon-o-calendar-days')
                    ->color('success'),
                
                Stat::make('Total Contestants', Contestant::count())
                    ->description('All contestants registered')
                    ->descriptionIcon('heroicon-o-users')
                    ->color('info'),
                
                Stat::make('Total Judges', Judge::count())
                    ->description('All judges in the system')
                    ->descriptionIcon('heroicon-o-user-circle')
                    ->color('warning'),
            ];
        }

        // Regular users see stats from their organizations
        $organizationIds = $user->accessibleOrganizationIds();

        return [
            Stat::make('My Events', Event::whereIn('organization_id', $organizationIds)->count())
                ->description('Events in your organizations')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('success'),
            
            Stat::make('My Contestants', Contestant::whereIn('organization_id', $organizationIds)->count())
                ->description('Contestants in your organizations')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),
            
            Stat::make('My Judges', Judge::whereIn('organization_id', $organizationIds)->count())
                ->description('Judges in your organizations')
                ->descriptionIcon('heroicon-o-user-circle')
                ->color('warning'),
        ];
    }
}
