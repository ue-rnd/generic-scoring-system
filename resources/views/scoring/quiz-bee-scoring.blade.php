@extends('layouts.public')

@section('title', 'Quiz Bee Scoring - ' . $event->name)
@section('page-title', $event->name)

@section('header-actions')
<div class="flex items-center gap-md">
    <button 
        id="autoSaveToggle"
        type="button"
        class="btn btn-secondary">
        <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <span id="autoSaveStatus">Auto-save: Off</span>
    </button>
    <a href="{{ $event->public_viewing_url }}" 
       target="_blank"
       class="btn btn-primary">
        <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        View Public Display
    </a>
</div>
@endsection

@section('content')
<div class="content-wrapper" style="max-width: 1600px;">
    <!-- Event Info -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Event Information</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-4 gap-lg">
                <div>
                    <div class="stat-label">Scoring Mode</div>
                    <div class="stat-value capitalize">{{ $event->scoring_mode }}</div>
                </div>
                <div>
                    <div class="stat-label">Total Contestants</div>
                    <div class="stat-value">{{ $event->contestants->count() }}</div>
                </div>
                <div>
                    <div class="stat-label">Total Rounds</div>
                    <div class="stat-value">{{ $event->rounds->count() }}</div>
                </div>
                <div>
                    <div class="stat-label">Total Questions</div>
                    <div class="stat-value">{{ $event->rounds->sum('total_questions') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scoring Interface -->
    <form id="scoringForm" method="POST" action="{{ route('admin.score.store', $event->admin_token) }}">
        @csrf

        @foreach($event->rounds as $round)
        <div class="card" style="margin-bottom: var(--spacing-lg);">
            <!-- Round Header -->
            <div class="card-gradient-header">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="card-gradient-header-title">{{ $round->name }}</h3>
                        @if($round->description)
                        <p class="card-gradient-header-subtitle">{{ $round->description }}</p>
                        @endif
                    </div>
                    <div style="text-align: right;">
                        <p class="text-sm text-white" style="opacity: 0.9;">{{ $round->total_questions }} Questions</p>
                        <p class="text-lg font-bold text-white">
                            {{ $event->scoring_mode === 'boolean' ? $round->points_per_question . ' pts/question' : 'Max: ' . $round->max_score }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Scoring Grid -->
            <div style="overflow-x: auto;">
                <table class="table quiz-bee-table">
                    <thead>
                        <tr>
                            <th class="sticky-column" style="text-align: left;">Contestant</th>
                            @for($q = 1; $q <= $round->total_questions; $q++)
                            <th style="text-align: center;">Q{{ $q }}</th>
                            @endfor
                            <th class="sticky-column-right" style="text-align: center;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($event->contestants as $contestant)
                        <tr>
                            <td class="sticky-column"><strong>{{ $contestant->name }}</strong></td>
                            @for($q = 1; $q <= $round->total_questions; $q++)
                            @php
                                $existingScore = $existingScores[$contestant->id][$round->id][$q] ?? null;
                            @endphp
                            <td style="text-align: center;">
                                @if($event->scoring_mode === 'boolean')
                                <!-- Boolean Mode: Checkbox -->
                                <div style="display: flex; justify-content: center;">
                                    <input type="hidden" name="scores[c{{ $contestant->id }}_r{{ $round->id }}_q{{ $q }}][contestant_id]" value="{{ $contestant->id }}">
                                    <input type="hidden" name="scores[c{{ $contestant->id }}_r{{ $round->id }}_q{{ $q }}][round_id]" value="{{ $round->id }}">
                                    <input type="hidden" name="scores[c{{ $contestant->id }}_r{{ $round->id }}_q{{ $q }}][question_number]" value="{{ $q }}">
                                    
                                    <label style="display: inline-flex; align-items: center; cursor: pointer;">
                                        <input 
                                            type="checkbox"
                                            name="scores[c{{ $contestant->id }}_r{{ $round->id }}_q{{ $q }}][is_correct]"
                                            value="1"
                                            {{ ($existingScore && $existingScore->is_correct) ? 'checked' : '' }}
                                            class="score-input form-checkbox"
                                            style="width: 1.25rem; height: 1.25rem;"
                                            data-contestant="{{ $contestant->id }}"
                                            data-round="{{ $round->id }}">
                                    </label>
                                </div>
                                @else
                                <!-- Manual Mode: Number Input -->
                                <input type="hidden" name="scores[c{{ $contestant->id }}_r{{ $round->id }}_q{{ $q }}][contestant_id]" value="{{ $contestant->id }}">
                                <input type="hidden" name="scores[c{{ $contestant->id }}_r{{ $round->id }}_q{{ $q }}][round_id]" value="{{ $round->id }}">
                                <input type="hidden" name="scores[c{{ $contestant->id }}_r{{ $round->id }}_q{{ $q }}][question_number]" value="{{ $q }}">
                                
                                <input 
                                    type="number"
                                    name="scores[c{{ $contestant->id }}_r{{ $round->id }}_q{{ $q }}][score]"
                                    value="{{ $existingScore->score ?? '' }}"
                                    min="0"
                                    max="{{ $round->points_per_question }}"
                                    step="0.01"
                                    class="score-input form-input"
                                    style="width: 5rem; text-align: center;"
                                    data-contestant="{{ $contestant->id }}"
                                    data-round="{{ $round->id }}"
                                    placeholder="0">
                                @endif
                            </td>
                            @endfor
                            <td class="sticky-column-right" style="text-align: center;">
                                <span class="contestant-total text-lg font-bold" data-contestant="{{ $contestant->id }}" data-round="{{ $round->id }}">
                                    @php
                                        $roundTotal = 0;
                                        if (isset($existingScores[$contestant->id][$round->id])) {
                                            foreach ($existingScores[$contestant->id][$round->id] as $score) {
                                                $roundTotal += $score->score;
                                            }
                                        }
                                    @endphp
                                    {{ number_format($roundTotal, 2) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach

        <!-- Submit Button -->
        <div class="sticky-bottom">
            <div class="flex justify-between items-center">
                <div id="saveStatus" class="text-sm text-gray-600"></div>
                <div class="flex gap-md">
                    <button 
                        type="button"
                        onclick="window.location.reload()"
                        class="btn btn-secondary">
                        <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reload
                    </button>
                    <button 
                        type="submit"
                        id="submitBtn"
                        class="btn btn-primary">
                        <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save All Scores
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
    const submitBtn = document.getElementById('submitBtn');
    const saveStatus = document.getElementById('saveStatus');
    const autoSaveToggle = document.getElementById('autoSaveToggle');
    const autoSaveStatus = document.getElementById('autoSaveStatus');
    const scoringMode = '{{ $event->scoring_mode }}';
    const pointsPerQuestion = @json($event->rounds->keyBy('id')->map(fn($r) => $r->points_per_question));
    
    let autoSaveEnabled = false;
    let autoSaveTimeout;
    let hasUnsavedChanges = false;

    // Toggle auto-save
    autoSaveToggle.addEventListener('click', function() {
        autoSaveEnabled = !autoSaveEnabled;
        autoSaveStatus.textContent = autoSaveEnabled ? 'Auto-save: On' : 'Auto-save: Off';
        
        if (autoSaveEnabled && hasUnsavedChanges) {
            scheduleAutoSave();
        }
    });

    // Calculate totals
    function calculateTotal(contestantId, roundId) {
        let total = 0;
        const inputs = document.querySelectorAll(`.score-input[data-contestant="${contestantId}"][data-round="${roundId}"]`);
        
        inputs.forEach(input => {
            if (scoringMode === 'boolean') {
                if (input.checked) {
                    total += parseFloat(pointsPerQuestion[roundId]);
                }
            } else {
                const value = parseFloat(input.value) || 0;
                total += value;
            }
        });
        
        const totalElement = document.querySelector(`.contestant-total[data-contestant="${contestantId}"][data-round="${roundId}"]`);
        if (totalElement) {
            totalElement.textContent = total.toFixed(2);
        }
    }

    // Listen for score changes
    const scoreInputs = document.querySelectorAll('.score-input');
    scoreInputs.forEach(input => {
        input.addEventListener('change', function() {
            const contestantId = this.dataset.contestant;
            const roundId = this.dataset.round;
            calculateTotal(contestantId, roundId);
            
            hasUnsavedChanges = true;
            saveStatus.textContent = 'Unsaved changes';
            saveStatus.style.color = 'var(--warning)';
            
            if (autoSaveEnabled) {
                scheduleAutoSave();
            }
        });

        // For number inputs, also update on input (real-time)
        if (input.type === 'number') {
            input.addEventListener('input', function() {
                const contestantId = this.dataset.contestant;
                const roundId = this.dataset.round;
                calculateTotal(contestantId, roundId);
            });
        }
    });

    // Schedule auto-save
    function scheduleAutoSave() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            saveScores();
        }, 3000); // 3 seconds delay
    }

    // Save scores via AJAX
    function saveScores() {
        const formData = new FormData(form);
        
        saveStatus.textContent = 'Saving...';
        saveStatus.style.color = 'var(--primary)';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hasUnsavedChanges = false;
                saveStatus.textContent = 'All changes saved';
                saveStatus.style.color = 'var(--success)';
                
                setTimeout(() => {
                    if (!hasUnsavedChanges) {
                        saveStatus.textContent = '';
                    }
                }, 3000);
            } else {
                throw new Error('Save failed');
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            saveStatus.textContent = 'Error saving';
            saveStatus.style.color = 'var(--danger)';
        });
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span>Saving...';
        
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hasUnsavedChanges = false;
                saveStatus.textContent = 'All changes saved successfully!';
                saveStatus.style.color = 'var(--success)';
                
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Save All Scores';
            } else {
                throw new Error(data.message || 'Save failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving scores. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Save All Scores';
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S to save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (hasUnsavedChanges) {
                saveScores();
            }
        }
    });

    // Warn before leaving with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});
</script>
@endpush
