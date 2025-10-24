<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quiz Bee Scoring: {{ $event->name }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles
    
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <style>
        /* CSS Grid for scoring table - NO Tailwind utilities */
        .scoring-grid {
            display: grid;
            gap: 1px;
            background-color: #e5e7eb;
            border: 2px solid #d1d5db;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .scoring-header {
            display: contents;
        }
        
        .scoring-row {
            display: contents;
        }
        
        .cell {
            background-color: white;
            padding: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .cell-header {
            background: linear-gradient(to bottom, #f9fafb, #f3f4f6);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #374151;
        }
        
        .cell-contestant {
            background-color: #fafafa;
            font-weight: 600;
            justify-content: flex-start;
            padding-left: 1rem;
        }
        
        .cell-question {
            min-height: 3rem;
        }
        
        .cell-total {
            background-color: #eff6ff;
            font-weight: 700;
            font-size: 1.125rem;
            color: #1e40af;
        }
        
        .sticky-col {
            position: sticky;
            left: 0;
            z-index: 10;
        }
        
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 20;
        }
        
        .round-content {
            display: none;
        }
        
        .round-content.active {
            display: block;
        }
    </style>
</head>
<body style="background-color: #f9fafb; min-height: 100vh; padding: 2rem;">
    <div style="max-width: 1600px; margin: 0 auto;">
        
        {{-- Event Header --}}
        <x-filament::section style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                        <x-filament::icon icon="heroicon-o-academic-cap" style="width: 2rem; height: 2rem; color: #3b82f6;" />
                        <h1 style="font-size: 1.875rem; font-weight: 700; color: #111827; margin: 0;">
                            {{ $event->name }}
                        </h1>
                    </div>
                    @if($event->description)
                        <p style="color: #6b7280; margin-top: 0.5rem;">{{ $event->description }}</p>
                    @endif
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <x-filament::button
                        tag="a"
                        href="{{ route('public.view', $event->public_viewing_token) }}"
                        target="_blank"
                        color="success"
                        icon="heroicon-o-presentation-chart-line"
                        size="lg">
                        View Public Results
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Quick Stats --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <x-filament::section>
                <div style="text-align: center;">
                    <x-filament::icon icon="heroicon-o-user-group" style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #8b5cf6;" />
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Contestants</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">{{ $event->contestants->count() }}</div>
                </div>
            </x-filament::section>
            
            <x-filament::section>
                <div style="text-align: center;">
                    <x-filament::icon icon="heroicon-o-arrow-path-rounded-square" style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #3b82f6;" />
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Rounds</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">{{ $event->rounds->count() }}</div>
                </div>
            </x-filament::section>
            
            <x-filament::section>
                <div style="text-align: center;">
                    <x-filament::icon icon="heroicon-o-calculator" style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #10b981;" />
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Scoring Mode</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 0.25rem; text-transform: capitalize;">{{ $event->scoring_mode }}</div>
                </div>
            </x-filament::section>
        </div>

        {{-- Success/Error Messages --}}
        @if (session('success'))
            <x-filament::section style="margin-bottom: 1.5rem; background-color: #d1fae5; border-color: #10b981;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <x-filament::icon icon="heroicon-o-check-circle" style="width: 1.5rem; height: 1.5rem; color: #059669;" />
                    <span style="color: #065f46; font-weight: 500;">{{ session('success') }}</span>
                </div>
            </x-filament::section>
        @endif

        @if ($errors->any())
            <x-filament::section style="margin-bottom: 1.5rem; background-color: #fee2e2; border-color: #f87171;">
                <div style="display: flex; align-items: start; gap: 0.75rem;">
                    <x-filament::icon icon="heroicon-o-x-circle" style="width: 1.5rem; height: 1.5rem; color: #dc2626;" />
                    <div>
                        <p style="color: #991b1b; font-weight: 500; margin-bottom: 0.5rem;">Please correct the following errors:</p>
                        <ul style="list-style-type: disc; padding-left: 1.5rem; color: #b91c1c;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Scoring Interface --}}
        <x-filament::section x-data="quizBeeScoring()">
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon icon="heroicon-o-clipboard-document-check" style="width: 1.5rem; height: 1.5rem;" />
                    <span>Quiz Bee Scoring</span>
                </div>
            </x-slot>

            {{-- Round Tabs --}}
            @if($event->rounds->count() > 0)
                <div style="border-bottom: 2px solid #e5e7eb; margin-bottom: 1.5rem;">
                    <div style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem;">
                        @foreach($event->rounds as $index => $round)
                            <button
                                type="button"
                                @click="currentRound = {{ $round->id }}"
                                :class="currentRound === {{ $round->id }} ? 'tab-active' : 'tab-inactive'"
                                class="tab-button"
                                style="padding: 0.75rem 1.5rem; border-radius: 0.375rem; font-weight: 600; font-size: 0.875rem; white-space: nowrap; transition: all 0.15s; border: 2px solid transparent; cursor: pointer;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <x-filament::icon icon="heroicon-o-flag" style="width: 1rem; height: 1rem;" />
                                    <span>{{ $round->name }}</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <style>
                    .tab-active {
                        background-color: #3b82f6 !important;
                        color: white !important;
                        border-color: #2563eb !important;
                    }
                    .tab-inactive {
                        background-color: #f3f4f6 !important;
                        color: #4b5563 !important;
                    }
                    .tab-inactive:hover {
                        background-color: #e5e7eb !important;
                        color: #111827 !important;
                    }
                </style>

                {{-- Round Content --}}
                @foreach($event->rounds as $round)
                    <div 
                        x-show="currentRound === {{ $round->id }}"
                        x-transition
                        class="round-content"
                        :class="{ 'active': currentRound === {{ $round->id }} }">
                        
                        {{-- Round Info --}}
                        <div style="margin-bottom: 1rem; padding: 1rem; background-color: #eff6ff; border-radius: 0.5rem; border-left: 4px solid #3b82f6;">
                            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                                <div>
                                    <span style="font-size: 0.875rem; color: #6b7280;">Questions:</span>
                                    <x-filament::badge color="primary" size="lg">{{ $round->total_questions }}</x-filament::badge>
                                </div>
                                <div>
                                    <span style="font-size: 0.875rem; color: #6b7280;">Points per Question:</span>
                                    <x-filament::badge color="success" size="lg">{{ $round->points_per_question }}</x-filament::badge>
                                </div>
                                <div>
                                    <span style="font-size: 0.875rem; color: #6b7280;">Max Score:</span>
                                    <x-filament::badge color="warning" size="lg">{{ $round->max_score }}</x-filament::badge>
                                </div>
                            </div>
                        </div>

                        {{-- Scoring Grid --}}
                        <form method="POST" action="{{ route('admin.score.store', $event->admin_token) }}">
                            @csrf
                            <input type="hidden" name="round_id" value="{{ $round->id }}">
                            
                            <div style="overflow-x: auto; margin-bottom: 1.5rem;">
                                <div class="scoring-grid" style="grid-template-columns: 250px repeat({{ $round->total_questions }}, minmax(80px, 1fr)) 120px;">
                                    
                                    {{-- Header Row --}}
                                    <div class="scoring-header">
                                        <div class="cell cell-header sticky-col sticky-header">
                                            <x-filament::icon icon="heroicon-o-user-circle" style="width: 1rem; height: 1rem; margin-right: 0.5rem;" />
                                            Contestant
                                        </div>
                                        
                                        @for($q = 1; $q <= $round->total_questions; $q++)
                                            <div class="cell cell-header sticky-header">
                                                Q{{ $q }}
                                            </div>
                                        @endfor
                                        
                                        <div class="cell cell-header sticky-header">
                                            <x-filament::icon icon="heroicon-o-calculator" style="width: 1rem; height: 1rem; margin-right: 0.5rem;" />
                                            Total
                                        </div>
                                    </div>

                                    {{-- Contestant Rows --}}
                                    @foreach($event->contestants as $contestant)
                                        <div class="scoring-row">
                                            {{-- Contestant Name --}}
                                            <div class="cell cell-contestant sticky-col">
                                                <x-filament::avatar 
                                                    src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=3b82f6&background=dbeafe"
                                                    size="sm"
                                                    style="margin-right: 0.75rem;"
                                                />
                                                <div>
                                                    <div style="font-weight: 600; color: #111827;">{{ $contestant->name }}</div>
                                                    @if($contestant->description)
                                                        <div style="font-size: 0.75rem; color: #6b7280;">{{ Str::limit($contestant->description, 30) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            {{-- Question Cells --}}
                                            @for($q = 1; $q <= $round->total_questions; $q++)
                                                @php
                                                    $existingScore = $existingScores[$contestant->id][$round->id][$q] ?? null;
                                                @endphp
                                                
                                                <div class="cell cell-question">
                                                    @if($event->scoring_mode === 'boolean')
                                                        {{-- Boolean Mode: Checkbox --}}
                                                        <label style="display: flex; align-items: center; cursor: pointer;">
                                                            <x-filament::input.checkbox
                                                                name="scores[{{ $contestant->id }}_{{ $round->id }}_{{ $q }}][is_correct]"
                                                                value="1"
                                                                :checked="$existingScore?->is_correct ?? false"
                                                                x-model="scores.{{ $contestant->id }}_{{ $round->id }}_{{ $q }}"
                                                                @change="calculateTotal({{ $contestant->id }}, {{ $round->id }}, {{ $round->total_questions }}, {{ $round->points_per_question }})"
                                                            />
                                                            <input type="hidden" name="scores[{{ $contestant->id }}_{{ $round->id }}_{{ $q }}][contestant_id]" value="{{ $contestant->id }}">
                                                            <input type="hidden" name="scores[{{ $contestant->id }}_{{ $round->id }}_{{ $q }}][round_id]" value="{{ $round->id }}">
                                                            <input type="hidden" name="scores[{{ $contestant->id }}_{{ $round->id }}_{{ $q }}][question_number]" value="{{ $q }}">
                                                        </label>
                                                    @else
                                                        {{-- Manual Mode: Number Input --}}
                                                        <x-filament::input.wrapper>
                                                            <x-filament::input
                                                                type="number"
                                                                name="scores[{{ $contestant->id }}_{{ $round->id }}_{{ $q }}][score]"
                                                                min="0"
                                                                max="{{ $round->points_per_question }}"
                                                                step="0.5"
                                                                value="{{ $existingScore?->score ?? '' }}"
                                                                x-model.number="scores.{{ $contestant->id }}_{{ $round->id }}_{{ $q }}"
                                                                @input="calculateTotal({{ $contestant->id }}, {{ $round->id }}, {{ $round->total_questions }}, {{ $round->points_per_question }})"
                                                                style="width: 4rem; text-align: center;"
                                                            />
                                                        </x-filament::input.wrapper>
                                                        <input type="hidden" name="scores[{{ $contestant->id }}_{{ $round->id }}_{{ $q }}][contestant_id]" value="{{ $contestant->id }}">
                                                        <input type="hidden" name="scores[{{ $contestant->id }}_{{ $round->id }}_{{ $q }}][round_id]" value="{{ $round->id }}">
                                                        <input type="hidden" name="scores[{{ $contestant->id }}_{{ $round->id }}_{{ $q }}][question_number]" value="{{ $q }}">
                                                    @endif
                                                </div>
                                            @endfor
                                            
                                            {{-- Total Cell --}}
                                            <div class="cell cell-total" x-text="totals.{{ $contestant->id }}_{{ $round->id }} || 0">
                                                @php
                                                    $roundScores = $existingScores[$contestant->id][$round->id] ?? collect();
                                                    $total = $roundScores->sum('score');
                                                @endphp
                                                {{ $total }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 2px solid #e5e7eb;">
                                <x-filament::button
                                    type="submit"
                                    size="lg"
                                    color="primary"
                                    icon="heroicon-o-check-circle">
                                    Save Scores
                                </x-filament::button>
                            </div>
                        </form>
                    </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 3rem;">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" style="width: 3rem; height: 3rem; margin: 0 auto 1rem; color: #f59e0b;" />
                    <p style="color: #6b7280; font-size: 1.125rem;">No rounds configured for this event.</p>
                    <p style="color: #9ca3af; margin-top: 0.5rem;">Please add rounds in the admin panel first.</p>
                </div>
            @endif
        </x-filament::section>

    </div>

    @filamentScripts
    
    <script>
        function quizBeeScoring() {
            return {
                currentRound: {{ $event->rounds->first()?->id ?? 'null' }},
                scores: {},
                totals: {},
                
                init() {
                    // Initialize scores from existing data
                    @foreach($event->contestants as $contestant)
                        @foreach($event->rounds as $round)
                            @for($q = 1; $q <= $round->total_questions; $q++)
                                @php
                                    $existingScore = $existingScores[$contestant->id][$round->id][$q] ?? null;
                                @endphp
                                this.scores['{{ $contestant->id }}_{{ $round->id }}_{{ $q }}'] = {{ $event->scoring_mode === 'boolean' ? ($existingScore?->is_correct ? 'true' : 'false') : ($existingScore?->score ?? 0) }};
                            @endfor
                            
                            // Calculate initial totals
                            this.calculateTotal({{ $contestant->id }}, {{ $round->id }}, {{ $round->total_questions }}, {{ $round->points_per_question }});
                        @endforeach
                    @endforeach
                },
                
                calculateTotal(contestantId, roundId, totalQuestions, pointsPerQuestion) {
                    let total = 0;
                    
                    @if($event->scoring_mode === 'boolean')
                        // Boolean mode: count checked questions
                        for (let q = 1; q <= totalQuestions; q++) {
                            const key = `${contestantId}_${roundId}_${q}`;
                            if (this.scores[key]) {
                                total += pointsPerQuestion;
                            }
                        }
                    @else
                        // Manual mode: sum scores
                        for (let q = 1; q <= totalQuestions; q++) {
                            const key = `${contestantId}_${roundId}_${q}`;
                            total += parseFloat(this.scores[key] || 0);
                        }
                    @endif
                    
                    this.totals[`${contestantId}_${roundId}`] = total;
                }
            }
        }
    </script>
</body>
</html>
