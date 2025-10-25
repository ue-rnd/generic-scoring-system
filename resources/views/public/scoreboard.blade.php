@extends('layouts.public')

@section('title', 'Public Scoreboard - ' . $event->name)
@section('page-title', $event->name)

@php
    $subtitle = $event->is_active ? 'Live Scoreboard' : 'Event Ended';
@endphp

@section('header-actions')
<div class="flex items-center gap-md">
    @if($event->is_active)
    <div class="flex items-center gap-sm">
        <span class="live-indicator"></span>
        <span class="text-sm font-medium text-gray-700">Live</span>
    </div>
    @endif
    <button 
        id="refreshBtn"
        type="button"
        onclick="refreshScores()"
        class="btn btn-secondary">
        <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Refresh
    </button>
</div>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Event Statistics -->
    <div class="stats-grid" style="margin-bottom: var(--spacing-xl);">
        <div class="stat-card">
            <div class="stat-card-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="stat-card-content">
                <div class="stat-label">Contestants</div>
                <div class="stat-value">{{ $statistics['total_contestants'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon">
                @if($statistics['is_quiz_bee'])
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                @else
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                @endif
            </div>
            <div class="stat-card-content">
                @if($statistics['is_quiz_bee'])
                <div class="stat-label">Total Questions</div>
                <div class="stat-value">{{ $statistics['total_questions'] ?? 0 }}</div>
                @else
                <div class="stat-label">Judges</div>
                <div class="stat-value">{{ $statistics['active_judges'] }}/{{ $statistics['total_judges'] }}</div>
                @endif
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <div class="stat-card-content">
                <div class="stat-label">Completion</div>
                <div class="stat-value">{{ $statistics['completion_percentage'] }}%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="stat-card-content">
                <div class="stat-label">Last Update</div>
                <div class="stat-value" id="lastUpdate">Just now</div>
            </div>
        </div>
    <!-- Leaderboard -->
    <div class="card">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="card-title">
                    @if($config['show_rankings'])
                        Leaderboard
                    @else
                        Contestants
                    @endif
                </h3>
                @if(!$config['show_scores'])
                <span class="badge badge-secondary">
                    <svg style="margin-right: 0.25rem; height: 0.75rem; width: 0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Scores Hidden
                </span>
                @endif
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        @if($config['show_rankings'])
                        <th style="text-align: left;">Rank</th>
                        @endif
                        <th style="text-align: left;">Contestant</th>
                        @if($config['show_scores'])
                        <th style="text-align: center;">Score</th>
                        @endif
                        @if($config['show_criteria_breakdown'] && $event->judging_type === 'criteria')
                        @foreach($event->criterias as $criteria)
                        <th style="text-align: center;">{{ $criteria->name }}</th>
                        @endforeach
                        @endif
                        @if($config['show_round_breakdown'] && $event->judging_type === 'rounds')
                        @foreach($event->rounds as $round)
                        <th style="text-align: center;">{{ $round->name }}</th>
                        @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody id="leaderboardBody">
                    @foreach($results as $result)
                    <tr>
                        @if($config['show_rankings'])
                        <td>
                            @if($result['rank'] <= 3)
                            <div style="display: flex; align-items: center;">
                                <span class="rank-badge rank-badge-{{ $result['rank'] }}">
                                    {{ $result['rank'] }}
                                </span>
                            </div>
                            @else
                            <span class="text-sm font-medium">{{ $result['rank'] }}</span>
                            @endif
                        </td>
                        @endif
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div>
                                    <div class="text-sm font-medium">
                                        {{ $result['contestant']->name }}
                                    </div>
                                    @if($result['contestant']->description)
                                    <div class="text-sm text-gray-600">
                                        {{ $result['contestant']->description }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        @if($config['show_scores'])
                        <td style="text-align: center;">
                            <span class="text-lg font-bold">
                                {{ number_format($result['final_score'], 2) }}
                            </span>
                        </td>
                        @endif
                        @if($config['show_criteria_breakdown'] && $event->judging_type === 'criteria')
                        @foreach($event->criterias as $criteria)
                        <td style="text-align: center;" class="text-sm">
                            -
                        </td>
                        @endforeach
                        @endif
                        @if($config['show_round_breakdown'] && $event->judging_type === 'rounds')
                        @foreach($event->rounds as $round)
                        <td style="text-align: center;" class="text-sm">
                            -
                        </td>
                        @endforeach
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>    @if($config['show_judge_progress'] && !$statistics['is_quiz_bee'] && $judgeSummary->isNotEmpty())
    <!-- Judge Progress -->
    <div class="card" style="margin-top: var(--spacing-xl);">
        <div class="card-header">
            <h3 class="card-title">Judge Progress</h3>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
                @foreach($judgeSummary as $summary)
                <div>
                    <div class="flex justify-between items-center" style="margin-bottom: var(--spacing-sm);">
                        <span class="text-sm font-medium text-gray-700">
                            @if($config['show_judge_names'])
                                {{ $summary['judge']->name }}
                            @else
                                Judge #{{ $loop->iteration }}
                            @endif
                        </span>
                        <span class="text-sm font-medium text-gray-700">
                            {{ number_format($summary['completion_percentage'], 1) }}%
                        </span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: {{ $summary['completion_percentage'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
let lastUpdateTime = new Date();
let autoRefreshInterval;

function refreshScores() {
    const refreshBtn = document.getElementById('refreshBtn');
    const originalContent = refreshBtn.innerHTML;
    
    refreshBtn.disabled = true;
    refreshBtn.innerHTML = '<span class="spinner"></span>Refreshing...';
    
    fetch('{{ route("public.live", $event->public_viewing_token) }}')
        .then(response => response.json())
        .then(data => {
            // Update results would go here
            lastUpdateTime = new Date();
            updateLastUpdateDisplay();
            
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = originalContent;
        })
        .catch(error => {
            console.error('Error refreshing scores:', error);
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = originalContent;
        });
}

function updateLastUpdateDisplay() {
    const lastUpdate = document.getElementById('lastUpdate');
    const now = new Date();
    const diff = Math.floor((now - lastUpdateTime) / 1000);
    
    if (diff < 60) {
        lastUpdate.textContent = 'Just now';
    } else if (diff < 3600) {
        const minutes = Math.floor(diff / 60);
        lastUpdate.textContent = `${minutes}m ago`;
    } else {
        const hours = Math.floor(diff / 3600);
        lastUpdate.textContent = `${hours}h ago`;
    }
}

// Update "last update" display every 30 seconds
setInterval(updateLastUpdateDisplay, 30000);

// Auto-refresh every 30 seconds if event is active
@if($event->is_active)
autoRefreshInterval = setInterval(refreshScores, 30000);
@endif

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
</script>
@endpush
