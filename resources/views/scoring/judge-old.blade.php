<!DOCTYPE html>
<html lang="en" class="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Score Event: {{ $event->name }}</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @filamentStyles
    
    <style>
        body {
            margin: 0;
            padding: 2rem 1rem;
            min-height: 100vh;
        }
        
        .scoring-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-spacing {
            margin-bottom: 1.5rem;
        }
        
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .stat-icon {
            width: 2rem;
            height: 2rem;
            margin: 0 auto 0.5rem;
        }
        
        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            opacity: 0.7;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 0.25rem;
        }
        
        .scoring-table {
            width: 100%;
            overflow-x: auto;
        }
        
        .scoring-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .scoring-table th,
        .scoring-table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .scoring-table thead tr {
            background-image: linear-gradient(to bottom, rgba(0,0,0,0.02), rgba(0,0,0,0.04));
            border-bottom: 2px solid rgba(0,0,0,0.1);
        }
        
        .scoring-table th {
            font-weight: bold;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .scoring-table tbody tr:nth-child(odd) td:first-child {
            background-color: rgba(0,0,0,0.02);
        }
        
        .contestant-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .contestant-name {
            font-weight: bold;
            font-size: 0.875rem;
        }
        
        .contestant-desc {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.125rem;
        }
        
        .score-input-wrapper {
            max-width: 5rem;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.15s;
        }
        
        .checkbox-label:hover {
            background-color: rgba(0,0,0,0.05);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(0,0,0,0.1);
        }
        
        .info-banner {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .badge-group {
            display: flex;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: normal;
            flex-wrap: wrap;
        }
    </style>
</head>
<body class="fi-body">
    <div class="scoring-container">
        {{-- Event Header --}}
        <x-filament::section class="section-spacing">
            <div class="header-flex">
                <div>
                    <h1 style="font-size: 1.875rem; font-weight: bold; margin-bottom: 0.5rem;">{{ $event->name }}</h1>
                    @if($event->description)
                        <p style="opacity: 0.8; margin-bottom: 0.75rem;">{{ $event->description }}</p>
                    @endif
                    <x-filament::badge color="info">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <x-filament::icon icon="heroicon-o-user-circle" style="width: 1rem; height: 1rem;" />
                            <span>Scoring as: {{ $judgeName }}</span>
                        </div>
                    </x-filament::badge>
                </div>
                <div>
                    <x-filament::button 
                        tag="a" 
                        href="{{ route('score.results', $token) }}" 
                        color="success"
                        icon="heroicon-o-presentation-chart-line">
                        View Results
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Success Message --}}
        @if (session('success'))
            <x-filament::section class="section-spacing" style="background-color: rgba(16, 185, 129, 0.1); border-color: rgb(16, 185, 129);">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <x-filament::icon icon="heroicon-o-check-circle" style="width: 1.5rem; height: 1.5rem; color: rgb(16, 185, 129);" />
                    <span style="font-weight: 500;">{{ session('success') }}</span>
                </div>
            </x-filament::section>
        @endif

        {{-- Error Messages --}}
        @if ($errors->any())
            <x-filament::section class="section-spacing" style="background-color: rgba(239, 68, 68, 0.1); border-color: rgb(239, 68, 68);">
                <div style="display: flex; align-items: start; gap: 0.75rem;">
                    <x-filament::icon icon="heroicon-o-x-circle" style="width: 1.5rem; height: 1.5rem; color: rgb(239, 68, 68); flex-shrink: 0;" />
                    <div style="flex: 1;">
                        <p style="font-weight: 500; margin-bottom: 0.5rem;">Please correct the following errors:</p>
                        <ul style="list-style-type: disc; padding-left: 1.5rem;">
                            @foreach ($errors->all() as $error)
                                <li style="margin: 0.25rem 0;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Event Stats --}}
        <div class="stats-grid section-spacing">
            <x-filament::section>
                <div class="stat-card">
                    <x-filament::icon icon="heroicon-o-square-3-stack-3d" class="stat-icon" style="color: rgb(59, 130, 246);" />
                    <div class="stat-label">Event Type</div>
                    <div class="stat-value">{{ ucfirst($event->judging_type) }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="stat-card">
                    <x-filament::icon icon="heroicon-o-calculator" class="stat-icon" style="color: rgb(16, 185, 129);" />
                    <div class="stat-label">Scoring Mode</div>
                    <div class="stat-value">{{ ucfirst($event->scoring_mode) }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="stat-card">
                    <x-filament::icon icon="heroicon-o-user-group" class="stat-icon" style="color: rgb(245, 158, 11);" />
                    <div class="stat-label">Contestants</div>
                    <div class="stat-value">{{ $contestants->count() }}</div>
                </div>
            </x-filament::section>
            @if ($event->judging_type === 'criteria')
                <x-filament::section>
                    <div class="stat-card">
                        <x-filament::icon icon="heroicon-o-clipboard-document-list" class="stat-icon" style="color: rgb(139, 92, 246);" />
                        <div class="stat-label">Criteria</div>
                        <div class="stat-value">{{ $criterias->count() }}</div>
                    </div>
                </x-filament::section>
            @else
                <x-filament::section>
                    <div class="stat-card">
                        <x-filament::icon icon="heroicon-o-arrow-path-rounded-square" class="stat-icon" style="color: rgb(139, 92, 246);" />
                        <div class="stat-label">Rounds</div>
                        <div class="stat-value">{{ $rounds->count() }}</div>
                    </div>
                </x-filament::section>
            @endif
        </div>

        {{-- Scoring Form --}}
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon icon="heroicon-o-clipboard-document-check" style="width: 1.25rem; height: 1.25rem;" />
                    <span>Enter Scores</span>
                </div>
            </x-slot>

            <form method="POST" action="{{ route('score.store', $token) }}" x-data="scoringForm()">
                @csrf
                
                <div class="scoring-table">
                @if ($event->judging_type === 'criteria')
                    {{-- Criteria-based Scoring --}}
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <x-filament::icon icon="heroicon-o-user" style="width: 1rem; height: 1rem;" />
                                        Contestant
                                    </div>
                                </th>
                                @foreach ($criterias as $criteria)
                                    <th>
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                            <x-filament::icon icon="heroicon-o-star" style="width: 1rem; height: 1rem; color: rgb(245, 158, 11);" />
                                            <span>{{ $criteria->name }}</span>
                                        </div>
                                        <div class="badge-group">
                                            <x-filament::badge size="sm" color="success">Max: {{ $criteria->max_score }}</x-filament::badge>
                                            <x-filament::badge size="sm" color="info">Weight: {{ $criteria->weight }}</x-filament::badge>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contestants as $contestant)
                                <tr>
                                    <td>
                                        <div class="contestant-cell">
                                            <x-filament::avatar 
                                                src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=7F9CF5&background=EBF4FF"
                                                size="md"
                                            />
                                            <div>
                                                <div class="contestant-name">{{ $contestant->name }}</div>
                                                @if ($contestant->description)
                                                    <div class="contestant-desc">{{ $contestant->description }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @foreach ($criterias as $criteria)
                                        @php
                                            $key = $contestant->id . '_' . $criteria->id;
                                            $existingScore = $existingScores[$key] ?? null;
                                        @endphp
                                        <td>
                                            <div class="score-input-wrapper">
                                                <x-filament::input.wrapper>
                                                    <x-filament::input
                                                        type="number"
                                                        name="scores[{{ $key }}][score]"
                                                        x-model="scores['{{ $key }}'].score"
                                                        min="{{ $criteria->min_score ?? 0 }}"
                                                        max="{{ $criteria->max_score }}"
                                                        step="0.1"
                                                        value="{{ $existingScore?->score ?? '' }}"
                                                    />
                                                </x-filament::input.wrapper>
                                            </div>
                                            <input type="hidden" name="scores[{{ $key }}][contestant_id]" value="{{ $contestant->id }}">
                                            <input type="hidden" name="scores[{{ $key }}][criteria_id]" value="{{ $criteria->id }}">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    {{-- Rounds-based Scoring --}}
                    @if ($event->scoring_mode === 'boolean')
                        {{-- Boolean Mode --}}
                        <table>
                            <thead>
                                <tr>
                                    <th>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <x-filament::icon icon="heroicon-o-user" style="width: 1rem; height: 1rem;" />
                                            Contestant
                                        </div>
                                    </th>
                                    @foreach ($rounds as $round)
                                        <th>
                                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                <x-filament::icon icon="heroicon-o-arrow-path" style="width: 1rem; height: 1rem; color: rgb(59, 130, 246);" />
                                                <span>{{ $round->name }}</span>
                                            </div>
                                            <div class="badge-group">
                                                <x-filament::badge size="sm" color="success">{{ $round->points_per_question }} pts/Q</x-filament::badge>
                                                <x-filament::badge size="sm" color="info">{{ $round->total_questions }} Qs</x-filament::badge>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contestants as $contestant)
                                    <tr>
                                        <td>
                                            <div class="contestant-cell">
                                                <x-filament::avatar 
                                                    src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=10B981&background=D1FAE5"
                                                    size="md"
                                                />
                                                <div>
                                                    <div class="contestant-name">{{ $contestant->name }}</div>
                                                    @if ($contestant->description)
                                                        <div class="contestant-desc">{{ $contestant->description }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        @foreach ($rounds as $round)
                                            @php
                                                $key = $contestant->id . '_' . $round->id;
                                                $existingScore = $existingScores[$key] ?? null;
                                            @endphp
                                            <td>
                                                <label class="checkbox-label" :style="scores['{{ $key }}'].is_correct ? 'background-color: rgba(16, 185, 129, 0.1);' : ''">
                                                    <x-filament::input.checkbox
                                                        name="scores[{{ $key }}][is_correct]"
                                                        value="1"
                                                        :checked="$existingScore?->is_correct ?? false"
                                                        x-model="scores['{{ $key }}'].is_correct"
                                                    />
                                                    <span style="font-size: 0.875rem; font-weight: 500;" x-text="scores['{{ $key }}'].is_correct ? '✓ Correct' : '✗ Incorrect'">
                                                        {{ $existingScore?->is_correct ? '✓ Correct' : '✗ Incorrect' }}
                                                    </span>
                                                </label>
                                                <input type="hidden" name="scores[{{ $key }}][contestant_id]" value="{{ $contestant->id }}">
                                                <input type="hidden" name="scores[{{ $key }}][round_id]" value="{{ $round->id }}">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        {{-- Manual Mode --}}
                        <table>
                            <thead>
                                <tr>
                                    <th>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <x-filament::icon icon="heroicon-o-user" style="width: 1rem; height: 1rem;" />
                                            Contestant
                                        </div>
                                    </th>
                                    @foreach ($rounds as $round)
                                        <th>
                                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                <x-filament::icon icon="heroicon-o-arrow-path" style="width: 1rem; height: 1rem; color: rgb(59, 130, 246);" />
                                                <span>{{ $round->name }}</span>
                                            </div>
                                            <div class="badge-group">
                                                <x-filament::badge size="sm" color="success">Max: {{ $round->max_score }}</x-filament::badge>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contestants as $contestant)
                                    <tr>
                                        <td>
                                            <div class="contestant-cell">
                                                <x-filament::avatar 
                                                    src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=6366F1&background=E0E7FF"
                                                    size="md"
                                                />
                                                <div>
                                                    <div class="contestant-name">{{ $contestant->name }}</div>
                                                    @if ($contestant->description)
                                                        <div class="contestant-desc">{{ $contestant->description }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        @foreach ($rounds as $round)
                                            @php
                                                $key = $contestant->id . '_' . $round->id;
                                                $existingScore = $existingScores[$key] ?? null;
                                            @endphp
                                            <td>
                                                <div class="score-input-wrapper">
                                                    <x-filament::input.wrapper>
                                                        <x-filament::input
                                                            type="number"
                                                            name="scores[{{ $key }}][score]"
                                                            x-model="scores['{{ $key }}'].score"
                                                            min="0"
                                                            max="{{ $round->max_score }}"
                                                            step="0.1"
                                                            value="{{ $existingScore?->score ?? '' }}"
                                                        />
                                                    </x-filament::input.wrapper>
                                                </div>
                                                <input type="hidden" name="scores[{{ $key }}][contestant_id]" value="{{ $contestant->id }}">
                                                <input type="hidden" name="scores[{{ $key }}][round_id]" value="{{ $round->id }}">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endif
                </div>

                <div class="form-actions">
                    <x-filament::button type="submit" icon="heroicon-o-check-circle">
                        Save Scores
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        <div class="info-banner">
            <x-filament::badge color="info">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon icon="heroicon-o-information-circle" style="width: 1rem; height: 1rem;" />
                    <span>Your scores are saved. You can return to this page anytime using your unique link.</span>
                </div>
            </x-filament::badge>
        </div>
    </div>

    @filamentScripts

    <script>
        function scoringForm() {
            return {
                scores: {},
                
                init() {
                    this.initializeScores();
                },
                
                initializeScores() {
                    @if ($event->judging_type === 'criteria')
                        @foreach ($contestants as $contestant)
                            @foreach ($criterias as $criteria)
                                @php $key = $contestant->id . '_' . $criteria->id; @endphp
                                this.scores['{{ $key }}'] = { score: {{ $existingScores[$key]->score ?? 0 }} };
                            @endforeach
                        @endforeach
                    @elseif ($event->scoring_mode === 'boolean')
                        @foreach ($contestants as $contestant)
                            @foreach ($rounds as $round)
                                @php $key = $contestant->id . '_' . $round->id; @endphp
                                this.scores['{{ $key }}'] = { is_correct: {{ $existingScores[$key]->is_correct ?? 'false' }} };
                            @endforeach
                        @endforeach
                    @else
                        @foreach ($contestants as $contestant)
                            @foreach ($rounds as $round)
                                @php $key = $contestant->id . '_' . $round->id; @endphp
                                this.scores['{{ $key }}'] = { score: {{ $existingScores[$key]->score ?? 0 }} };
                            @endforeach
                        @endforeach
                    @endif
                }
            }
        }
    </script>
</body>
</html>
