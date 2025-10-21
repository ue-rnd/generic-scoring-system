<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results: {{ $event->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8" style="max-width: 1400px;">
        {{-- Header --}}
        <x-filament::section class="mb-6">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                        <x-filament::icon icon="heroicon-o-trophy" class="w-8 h-8 text-yellow-500" />
                        <h1 class="text-3xl font-bold text-gray-900">{{ $event->name }} - Results</h1>
                    </div>
                    <x-filament::badge color="info" size="lg">
                        Viewing as: {{ $judgeName }}
                    </x-filament::badge>
                </div>
                <div>
                    <x-filament::button 
                        tag="a" 
                        href="{{ route('score.judge', $token) }}" 
                        icon="heroicon-o-arrow-left"
                        size="lg">
                        Back to Scoring
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Current Rankings --}}
        <x-filament::section class="mb-6">
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon icon="heroicon-o-chart-bar" class="w-6 h-6" />
                    <span>Current Rankings</span>
                </div>
            </x-slot>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead style="background: linear-gradient(to bottom, #fef3c7, #fde68a);">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-800 uppercase tracking-wider" style="border-bottom: 2px solid #fbbf24;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <x-filament::icon icon="heroicon-o-trophy" class="w-5 h-5 text-yellow-600" />
                                    Rank
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-800 uppercase tracking-wider" style="border-bottom: 2px solid #fbbf24;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <x-filament::icon icon="heroicon-o-user-circle" class="w-5 h-5 text-yellow-600" />
                                    Contestant
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-800 uppercase tracking-wider" style="border-bottom: 2px solid #fbbf24;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <x-filament::icon icon="heroicon-o-star" class="w-5 h-5 text-yellow-600" />
                                    Final Score
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($results as $result)
                            <tr class="hover:bg-yellow-50 transition-colors {{ $result['rank'] <= 3 ? 'bg-yellow-50/50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div style="display: flex; align-items: center; justify-content: center; min-width: 3rem;">
                                        @if ($result['rank'] === 1)
                                            <div style="font-size: 2.5rem; filter: drop-shadow(0 2px 4px rgba(234, 179, 8, 0.4));">🥇</div>
                                        @elseif ($result['rank'] === 2)
                                            <div style="font-size: 2.5rem; filter: drop-shadow(0 2px 4px rgba(156, 163, 175, 0.4));">🥈</div>
                                        @elseif ($result['rank'] === 3)
                                            <div style="font-size: 2.5rem; filter: drop-shadow(0 2px 4px rgba(205, 127, 50, 0.4));">🥉</div>
                                        @else
                                            <x-filament::badge color="gray" size="lg">
                                                #{{ $result['rank'] }}
                                            </x-filament::badge>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <x-filament::avatar 
                                            src="https://ui-avatars.com/api/?name={{ urlencode($result['contestant']->name) }}&color=FBBF24&background=FEF3C7"
                                            size="lg"
                                        />
                                        <div class="text-base font-bold text-gray-900">{{ $result['contestant']->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #3b82f6, #1d4ed8); padding: 0.5rem 1rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);">
                                        <x-filament::icon icon="heroicon-o-star" class="w-5 h-5 text-yellow-300" />
                                        <span class="text-white font-bold text-lg">{{ number_format($result['final_score'], 2) }}</span>
                                        <span class="text-blue-100 text-sm">points</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Judge Progress Summary --}}
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon icon="heroicon-o-users" class="w-6 h-6" />
                    <span>Judging Progress</span>
                </div>
            </x-slot>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                @foreach ($judgeSummary as $summary)
                    <x-filament::section style="border: 2px solid {{ $summary['completion_percentage'] == 100 ? '#10b981' : ($summary['completion_percentage'] >= 50 ? '#f59e0b' : '#ef4444') }};">
                        <div style="display: flex; align-items: start; gap: 0.75rem; margin-bottom: 1rem;">
                            <x-filament::avatar 
                                src="https://ui-avatars.com/api/?name={{ urlencode($summary['judge']->name ?? 'Judge') }}&color={{ $summary['completion_percentage'] == 100 ? '10b981' : ($summary['completion_percentage'] >= 50 ? 'f59e0b' : 'ef4444') }}&background={{ $summary['completion_percentage'] == 100 ? 'd1fae5' : ($summary['completion_percentage'] >= 50 ? 'fef3c7' : 'fee2e2') }}"
                                size="lg"
                            />
                            <div style="flex: 1;">
                                <div class="font-bold text-gray-900 text-base">{{ $summary['judge']->name ?? 'Judge' }}</div>
                                <div class="text-xs text-gray-500 mt-1" style="display: flex; align-items: center; gap: 0.25rem;">
                                    <x-filament::icon icon="heroicon-o-check-circle" class="w-3 h-3" />
                                    {{ $summary['scores_count'] }} scores submitted
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <span class="text-sm font-medium text-gray-700">Progress</span>
                                <x-filament::badge 
                                    :color="$summary['completion_percentage'] == 100 ? 'success' : ($summary['completion_percentage'] >= 50 ? 'warning' : 'danger')"
                                    size="lg">
                                    {{ number_format($summary['completion_percentage'], 1) }}%
                                </x-filament::badge>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3" style="box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                                <div class="h-3 rounded-full transition-all duration-500" 
                                     style="width: {{ $summary['completion_percentage'] }}%; 
                                            background: {{ $summary['completion_percentage'] == 100 ? 'linear-gradient(90deg, #10b981, #059669)' : ($summary['completion_percentage'] >= 50 ? 'linear-gradient(90deg, #f59e0b, #d97706)' : 'linear-gradient(90deg, #ef4444, #dc2626)') }}; 
                                            box-shadow: 0 2px 4px rgba(0,0,0,0.15);"></div>
                            </div>
                        </div>
                        
                        @if ($summary['completion_percentage'] == 100)
                            <div class="mt-3 text-center">
                                <x-filament::badge color="success" size="lg">
                                    <x-filament::icon icon="heroicon-o-check-badge" class="w-4 h-4" />
                                    Complete
                                </x-filament::badge>
                            </div>
                        @endif
                    </x-filament::section>
                @endforeach
            </div>
        </x-filament::section>

        <div style="margin-top: 1.5rem; text-align: center;">
            <x-filament::badge color="info" size="lg">
                <x-filament::icon icon="heroicon-o-arrow-path" class="w-4 h-4" />
                Results are calculated in real-time as scores are submitted
            </x-filament::badge>
        </div>
    </div>

    @filamentScripts
</body>
</html>
