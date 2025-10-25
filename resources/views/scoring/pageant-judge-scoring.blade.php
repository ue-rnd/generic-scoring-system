@extends('layouts.public')

@section('title', 'Judge Scoring - ' . $event->name)
@section('page-title', $event->name)

@section('header-actions')
<div class="flex items-center gap-md">
    <span class="text-sm text-gray-600">
        Judge: <strong>{{ $judgeName }}</strong>
    </span>
    <a href="{{ route('score.results', $token) }}" class="btn btn-primary">
        <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        View Results
    </a>
</div>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Event Info -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Event Information</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-3 gap-lg">
                <div>
                    <div class="stat-label">Event Type</div>
                    <div class="stat-value capitalize">{{ $event->judging_type }} Based</div>
                </div>
                <div>
                    <div class="stat-label">Total Contestants</div>
                    <div class="stat-value">{{ $contestants->count() }}</div>
                </div>
                <div>
                    <div class="stat-label">Total {{ $event->judging_type === 'criteria' ? 'Criteria' : 'Rounds' }}</div>
                    <div class="stat-value">{{ $event->judging_type === 'criteria' ? $criterias->count() : $rounds->count() }}</div>
                </div>
            </div>
            
            @if($event->description)
            <div style="margin-top: var(--spacing-lg); padding-top: var(--spacing-lg); border-top: 1px solid var(--gray-200);">
                <p class="text-sm text-gray-600">{{ $event->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Scoring Form -->
    <form id="scoringForm" method="POST" action="{{ route('score.store', $token) }}">
        @csrf

        @if($event->judging_type === 'criteria')
            <!-- Criteria-Based Scoring -->
            @foreach($contestants as $contestant)
            <div class="contestant-card">
                <div class="card-colored-header">
                    <h3 class="card-colored-header-title">{{ $contestant->name }}</h3>
                    @if($contestant->description)
                    <p class="card-colored-header-subtitle">{{ $contestant->description }}</p>
                    @endif
                </div>
                
                <div class="card-body">
                    <div class="score-grid">
                        @foreach($criterias as $criteria)
                        @php
                            $key = $contestant->id . '_' . $criteria->id;
                            $existingScore = $existingScores->get($key);
                        @endphp
                        <div class="form-group">
                            <label class="form-label">
                                {{ $criteria->name }}
                                @if($criteria->description)
                                <span class="form-hint">{{ $criteria->description }}</span>
                                @endif
                                <span class="form-hint">
                                    Range: {{ $criteria->min_score }} - {{ $criteria->max_score }} (Weight: {{ $criteria->weight }}%)
                                </span>
                            </label>
                            
                            <input type="hidden" name="scores[{{ $loop->parent->index }}_{{ $loop->index }}][contestant_id]" value="{{ $contestant->id }}">
                            <input type="hidden" name="scores[{{ $loop->parent->index }}_{{ $loop->index }}][criteria_id]" value="{{ $criteria->id }}">
                            
                            <input 
                                type="number" 
                                name="scores[{{ $loop->parent->index }}_{{ $loop->index }}][score]" 
                                min="{{ $criteria->min_score }}" 
                                max="{{ $criteria->max_score }}" 
                                step="0.01"
                                value="{{ $existingScore->score ?? '' }}"
                                class="form-input"
                                placeholder="Enter score">
                            
                            <textarea 
                                name="scores[{{ $loop->parent->index }}_{{ $loop->index }}][comments]" 
                                rows="2"
                                class="form-input form-textarea"
                                placeholder="Comments (optional)">{{ $existingScore->comments ?? '' }}</textarea>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <!-- Rounds-Based Scoring -->
            @foreach($contestants as $contestant)
            <div class="contestant-card">
                <div class="card-colored-header">
                    <h3 class="card-colored-header-title">{{ $contestant->name }}</h3>
                    @if($contestant->description)
                    <p class="card-colored-header-subtitle">{{ $contestant->description }}</p>
                    @endif
                </div>
                
                <div class="card-body">
                    <div class="score-grid">
                        @foreach($rounds as $round)
                        @php
                            $key = $contestant->id . '_' . $round->id;
                            $existingScore = $existingScores->get($key);
                        @endphp
                        <div class="form-group">
                            <label class="form-label">
                                {{ $round->name }}
                                @if($round->description)
                                <span class="form-hint">{{ $round->description }}</span>
                                @endif
                                <span class="form-hint">Max Score: {{ $round->max_score }}</span>
                            </label>
                            
                            <input type="hidden" name="scores[{{ $loop->parent->index }}_{{ $loop->index }}][contestant_id]" value="{{ $contestant->id }}">
                            <input type="hidden" name="scores[{{ $loop->parent->index }}_{{ $loop->index }}][round_id]" value="{{ $round->id }}">
                            
                            @if($event->scoring_mode === 'boolean')
                            <!-- Boolean scoring -->
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input 
                                    type="checkbox" 
                                    name="scores[{{ $loop->parent->index }}_{{ $loop->index }}][is_correct]" 
                                    value="1"
                                    {{ ($existingScore && $existingScore->is_correct) ? 'checked' : '' }}
                                    class="form-checkbox">
                                <span class="text-sm text-gray-700">Mark as Correct ({{ $round->points_per_question }} points)</span>
                            </label>
                            @else
                            <!-- Manual scoring -->
                            <input 
                                type="number" 
                                name="scores[{{ $loop->parent->index }}_{{ $loop->index }}][score]" 
                                min="0" 
                                max="{{ $round->max_score }}" 
                                step="0.01"
                                value="{{ $existingScore->score ?? '' }}"
                                class="form-input"
                                placeholder="Enter score">
                            @endif
                            
                            <textarea 
                                name="scores[{{ $loop->parent->index }}_{{ $loop->index }}][comments]" 
                                rows="2"
                                class="form-input form-textarea"
                                placeholder="Comments (optional)">{{ $existingScore->comments ?? '' }}</textarea>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        @endif

        <!-- Submit Buttons -->
        <div class="sticky-bottom">
            <div class="flex justify-between items-center">
                <div></div>
                <div class="flex gap-md">
                    <button type="button" onclick="window.location.reload()" class="btn btn-secondary">
                        Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Submit Scores
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scoringForm');
    
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner" style="margin-right: 0.5rem;"></span>Saving...';
    });
});
</script>
@endpush
