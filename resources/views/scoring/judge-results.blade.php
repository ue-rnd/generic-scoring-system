@extends('layouts.public')

@section('title', 'Results - ' . $event->name)
@section('page-title', $event->name)

@php
    $subtitle = 'Event Results';
@endphp

@section('header-actions')
<div class="flex items-center gap-md">
    <span class="text-sm text-gray-600">
        Viewing as: <strong>{{ $judgeName }}</strong>
    </span>
    <a href="{{ route('score.judge', $token) }}" class="btn btn-secondary">
        <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
        Back to Scoring
    </a>
    <a href="{{ $event->public_viewing_url }}" target="_blank" class="btn btn-primary">
        <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        View Public Display
    </a>
</div>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Event Summary -->
    <div class="card" style="margin-bottom: var(--spacing-xl);">
        <div class="card-header">
            <h3 class="card-title">Event Summary</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-4 gap-lg">
                <div>
                    <dt class="stat-label">Event Type</dt>
                    <dd class="stat-value capitalize">{{ $event->judging_type }} Based</dd>
                </div>
                <div>
                    <dt class="stat-label">Total Contestants</dt>
                    <dd class="stat-value">{{ $results->count() }}</dd>
                </div>
                @if(!$event->isQuizBeeType())
                <div>
                    <dt class="stat-label">Total Judges</dt>
                    <dd class="stat-value">{{ $judgeSummary->count() }}</dd>
                </div>
                @endif
                <div>
                    <dt class="stat-label">Status</dt>
                    <dd style="margin-top: 0.25rem;">
                        <span class="badge {{ $event->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ $event->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </div>
            </div>
            
            @if($event->description)
            <div style="margin-top: var(--spacing-md); padding-top: var(--spacing-md); border-top: 1px solid var(--gray-200);">
                <p class="text-sm text-gray-600">{{ $event->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Final Rankings -->
    <div class="card" style="margin-bottom: var(--spacing-xl);">
        <div class="card-header">
            <h3 class="card-title">Final Rankings</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="text-align: left;">Rank</th>
                        <th style="text-align: left;">Contestant</th>
                        <th style="text-align: center;">Final Score</th>
                        <th style="text-align: center;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $result)
                    <tr>
                        <td>
                            @if($result['rank'] <= 3)
                            <div style="display: flex; align-items: center;">
                                <span class="rank-badge rank-badge-{{ $result['rank'] }}" style="width: 2.5rem; height: 2.5rem; font-size: 1.125rem;">
                                    @if($result['rank'] == 1) 🥇
                                    @elseif($result['rank'] == 2) 🥈
                                    @else 🥉
                                    @endif
                                </span>
                                <span style="margin-left: 0.5rem;" class="text-sm font-medium">{{ $result['rank'] }}</span>
                            </div>
                            @else
                            <span class="text-lg font-medium">{{ $result['rank'] }}</span>
                            @endif
                        </td>
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
                        <td style="text-align: center;">
                            <span style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
                                {{ number_format($result['final_score'], 2) }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <button 
                                onclick="toggleDetails({{ $result['contestant']->id }})"
                                class="btn btn-sm"
                                style="background-color: var(--primary-light); color: var(--primary); border: none;">
                                <svg style="margin-right: 0.25rem; height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                View Breakdown
                            </button>
                        </td>
                    </tr>
                    <tr id="details-{{ $result['contestant']->id }}" class="hidden" style="background-color: var(--gray-50);">
                        <td colspan="4" style="padding: var(--spacing-lg) var(--spacing-xl);">
                            <div class="text-sm">
                                <h4 style="font-weight: 500; color: var(--gray-900); margin-bottom: var(--spacing-sm);">Score Breakdown</h4>
                                
                                @if($event->judging_type === 'criteria')
                                <!-- Criteria Breakdown -->
                                <div class="grid grid-cols-3 gap-md">
                                    @foreach($event->criterias as $criteria)
                                    @php
                                        $scores = $result['contestant']->scores()
                                            ->where('criteria_id', $criteria->id)
                                            ->get();
                                        $average = $scores->avg('score');
                                    @endphp
                                    <div class="card" style="padding: var(--spacing-md);">
                                        <div class="flex justify-between items-start" style="margin-bottom: var(--spacing-sm);">
                                            <h5 class="font-medium">{{ $criteria->name }}</h5>
                                            <span class="text-sm text-gray-600">Weight: {{ $criteria->weight }}%</span>
                                        </div>
                                        <div style="margin-top: var(--spacing-sm);">
                                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
                                                {{ number_format($average, 2) }}
                                            </div>
                                            <div class="text-xs text-gray-600" style="margin-top: 0.25rem;">
                                                Avg of {{ $scores->count() }} score(s)
                                            </div>
                                        </div>
                                        @if($criteria->description)
                                        <p class="text-xs text-gray-600" style="margin-top: var(--spacing-sm);">{{ $criteria->description }}</p>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <!-- Rounds Breakdown -->
                                <div class="grid grid-cols-3 gap-md">
                                    @foreach($event->rounds as $round)
                                    @php
                                        $scores = $result['contestant']->scores()
                                            ->where('round_id', $round->id)
                                            ->get();
                                        $total = $scores->sum('score');
                                    @endphp
                                    <div class="card" style="padding: var(--spacing-md);">
                                        <div class="flex justify-between items-start" style="margin-bottom: var(--spacing-sm);">
                                            <h5 class="font-medium">{{ $round->name }}</h5>
                                            @if($event->scoring_mode === 'boolean')
                                            <span class="text-sm text-gray-600">
                                                {{ $scores->where('is_correct', true)->count() }}/{{ $round->total_questions }}
                                            </span>
                                            @endif
                                        </div>
                                        <div style="margin-top: var(--spacing-sm);">
                                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
                                                {{ number_format($total, 2) }}
                                            </div>
                                            <div class="text-xs text-gray-600" style="margin-top: 0.25rem;">
                                                Max: {{ $round->max_score }}
                                            </div>
                                        </div>
                                        @if($round->description)
                                        <p class="text-xs text-gray-600" style="margin-top: var(--spacing-sm);">{{ $round->description }}</p>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if(!$event->isQuizBeeType() && $judgeSummary->isNotEmpty())
    <!-- Judge Completion Status -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Judge Completion Status</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="text-align: left;">Judge</th>
                        <th style="text-align: center;">Scores Submitted</th>
                        <th style="text-align: center;">Completion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($judgeSummary as $summary)
                    <tr>
                        <td>
                            <div class="text-sm font-medium">
                                {{ $summary['judge']->name }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span class="text-sm">
                                {{ $summary['scores_count'] }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; justify-content: center;">
                                <div style="width: 8rem;">
                                    <div class="flex justify-between items-center" style="margin-bottom: 0.25rem;">
                                        <span class="text-xs font-medium text-gray-700">
                                            {{ number_format($summary['completion_percentage'], 1) }}%
                                        </span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-bar-fill
                                            @if($summary['completion_percentage'] >= 100) progress-bar-success
                                            @elseif($summary['completion_percentage'] >= 50) progress-bar-warning
                                            @else progress-bar-danger
                                            @endif" 
                                            style="width: {{ min($summary['completion_percentage'], 100) }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function toggleDetails(contestantId) {
    const detailsRow = document.getElementById(`details-${contestantId}`);
    detailsRow.classList.toggle('hidden');
}
</script>
@endpush
