<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Score Event: {{ $event->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @filamentStyles
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8" style="max-width: 1400px;">
        {{-- Event Header Section --}}
        <x-filament::section class="mb-6">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $event->name }}</h1>
                    <p class="text-gray-600 mt-2">{{ $event->description }}</p>
                    <div class="mt-3">
                        <x-filament::badge color="info" size="lg">
                            Scoring as: {{ $judgeName }}
                        </x-filament::badge>
                    </div>
                </div>
                <div>
                    <x-filament::button 
                        tag="a" 
                        href="{{ route('score.results', $token) }}" 
                        color="success"
                        icon="heroicon-o-presentation-chart-line"
                        size="lg">
                        View Results
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Success Message --}}
        @if (session('success'))
            <x-filament::section class="mb-6" style="background-color: #d1fae5; border-color: #34d399;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <x-filament::icon icon="heroicon-o-check-circle" class="w-6 h-6 text-green-600" />
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                </div>
            </x-filament::section>
        @endif

        {{-- Error Messages --}}
        @if ($errors->any())
            <x-filament::section class="mb-6" style="background-color: #fee2e2; border-color: #f87171;">
                <div style="display: flex; align-items: start; gap: 0.75rem;">
                    <x-filament::icon icon="heroicon-o-x-circle" class="w-6 h-6 text-red-600" />
                    <div>
                        <p class="text-red-800 font-medium mb-2">Please correct the following errors:</p>
                        <ul style="list-style-type: disc; padding-left: 1.5rem;">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-700">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Event Info Stats --}}
        <div class="mb-6" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <x-filament::section>
                <div style="text-align: center;">
                    <x-filament::icon icon="heroicon-o-square-3-stack-3d" class="w-8 h-8 mx-auto mb-2 text-primary-500" />
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Event Type</div>
                    <div class="text-xl font-bold text-gray-900 mt-1">{{ ucfirst($event->judging_type) }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div style="text-align: center;">
                    <x-filament::icon icon="heroicon-o-calculator" class="w-8 h-8 mx-auto mb-2 text-success-500" />
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Scoring Mode</div>
                    <div class="text-xl font-bold text-gray-900 mt-1">{{ ucfirst($event->scoring_mode) }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div style="text-align: center;">
                    <x-filament::icon icon="heroicon-o-user-group" class="w-8 h-8 mx-auto mb-2 text-warning-500" />
                    <div class="text-xs text-gray-500 uppercase tracking-wider">Contestants</div>
                    <div class="text-xl font-bold text-gray-900 mt-1">{{ $contestants->count() }}</div>
                </div>
            </x-filament::section>
            @if ($event->judging_type === 'criteria')
                <x-filament::section>
                    <div style="text-align: center;">
                        <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-8 h-8 mx-auto mb-2 text-info-500" />
                        <div class="text-xs text-gray-500 uppercase tracking-wider">Criteria</div>
                        <div class="text-xl font-bold text-gray-900 mt-1">{{ $criterias->count() }}</div>
                    </div>
                </x-filament::section>
            @else
                <x-filament::section>
                    <div style="text-align: center;">
                        <x-filament::icon icon="heroicon-o-arrow-path-rounded-square" class="w-8 h-8 mx-auto mb-2 text-info-500" />
                        <div class="text-xs text-gray-500 uppercase tracking-wider">Rounds</div>
                        <div class="text-xl font-bold text-gray-900 mt-1">{{ $rounds->count() }}</div>
                    </div>
                </x-filament::section>
            @endif
        </div>

        {{-- Scoring Form --}}
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon icon="heroicon-o-clipboard-document-check" class="w-6 h-6" />
                    <span>Enter Scores</span>
                </div>
            </x-slot>

            <form method="POST" action="{{ route('score.store', $token) }}" x-data="scoringForm()">
                @csrf
                
                <div class="overflow-x-auto">
                @if ($event->judging_type === 'criteria')
                    {{-- Criteria-based scoring --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead style="background: linear-gradient(to bottom, #f9fafb, #f3f4f6);">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider" style="border-bottom: 2px solid #e5e7eb;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <x-filament::icon icon="heroicon-o-user" class="w-4 h-4" />
                                        Contestant
                                    </div>
                                </th>
                                @foreach ($criterias as $criteria)
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider" style="border-bottom: 2px solid #e5e7eb;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                            <x-filament::icon icon="heroicon-o-star" class="w-4 h-4 text-warning-500" />
                                            <span>{{ $criteria->name }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 font-normal flex gap-2">
                                            <x-filament::badge size="sm" color="success">Max: {{ $criteria->max_score }}</x-filament::badge>
                                            <x-filament::badge size="sm" color="info">Weight: {{ $criteria->weight }}</x-filament::badge>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($contestants as $contestant)
                                <tr class="hover:bg-primary-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap" style="background-color: #fafafa;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <x-filament::avatar 
                                                src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=7F9CF5&background=EBF4FF"
                                                size="md"
                                            />
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $contestant->name }}</div>
                                                @if ($contestant->description)
                                                    <div class="text-xs text-gray-500">{{ $contestant->description }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @foreach ($criterias as $criteria)
                                        @php
                                            $key = $contestant->id . '_' . $criteria->id;
                                            $existingScore = $existingScores[$key] ?? null;
                                        @endphp
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-filament::input.wrapper>
                                                <x-filament::input
                                                    type="number"
                                                    name="scores[{{ $key }}][score]"
                                                    x-model="scores['{{ $key }}'].score"
                                                    min="{{ $criteria->min_score ?? 0 }}"
                                                    max="{{ $criteria->max_score }}"
                                                    step="0.1"
                                                    value="{{ $existingScore?->score ?? '' }}"
                                                    style="width: 5rem;"
                                                />
                                            </x-filament::input.wrapper>
                                            <input type="hidden" 
                                                   name="scores[{{ $key }}][contestant_id]" 
                                                   value="{{ $contestant->id }}">
                                            <input type="hidden" 
                                                   name="scores[{{ $key }}][criteria_id]" 
                                                   value="{{ $criteria->id }}">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    {{-- Rounds-based scoring --}}
                    @if ($event->scoring_mode === 'boolean')
                        {{-- Boolean Mode: Correct/Incorrect --}}
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead style="background: linear-gradient(to bottom, #f9fafb, #f3f4f6);">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider" style="border-bottom: 2px solid #e5e7eb;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <x-filament::icon icon="heroicon-o-user" class="w-4 h-4" />
                                            Contestant
                                        </div>
                                    </th>
                                    @foreach ($rounds as $round)
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider" style="border-bottom: 2px solid #e5e7eb;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                                <x-filament::icon icon="heroicon-o-arrow-path" class="w-4 h-4 text-primary-500" />
                                                <span>{{ $round->name }}</span>
                                            </div>
                                            <div class="text-xs text-gray-500 font-normal flex gap-2">
                                                <x-filament::badge size="sm" color="success">{{ $round->points_per_question }} pts/Q</x-filament::badge>
                                                <x-filament::badge size="sm" color="info">{{ $round->total_questions }} Qs</x-filament::badge>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($contestants as $contestant)
                                    <tr class="hover:bg-success-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap" style="background-color: #fafafa;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <x-filament::avatar 
                                                    src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=10B981&background=D1FAE5"
                                                    size="md"
                                                />
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900">{{ $contestant->name }}</div>
                                                    @if ($contestant->description)
                                                        <div class="text-xs text-gray-500">{{ $contestant->description }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        @foreach ($rounds as $round)
                                            @php
                                                $key = $contestant->id . '_' . $round->id;
                                                $existingScore = $existingScores[$key] ?? null;
                                            @endphp
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; padding: 0.5rem; border-radius: 0.375rem; transition: background-color 0.15s;" 
                                                       :style="scores['{{ $key }}'].is_correct ? 'background-color: #d1fae5;' : 'background-color: transparent;'"
                                                       class="hover:bg-gray-100">
                                                    <x-filament::input.checkbox
                                                        name="scores[{{ $key }}][is_correct]"
                                                        value="1"
                                                        :checked="$existingScore?->is_correct ?? false"
                                                        x-model="scores['{{ $key }}'].is_correct"
                                                    />
                                                    <span class="text-sm font-medium" 
                                                          :class="scores['{{ $key }}'].is_correct ? 'text-success-700' : 'text-gray-600'"
                                                          x-text="scores['{{ $key }}'].is_correct ? '✓ Correct' : '✗ Incorrect'">
                                                        {{ $existingScore?->is_correct ? '✓ Correct' : '✗ Incorrect' }}
                                                    </span>
                                                </label>
                                                <input type="hidden" 
                                                       name="scores[{{ $key }}][contestant_id]" 
                                                       value="{{ $contestant->id }}">
                                                <input type="hidden" 
                                                       name="scores[{{ $key }}][round_id]" 
                                                       value="{{ $round->id }}">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        {{-- Manual Mode: Enter Scores --}}
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead style="background: linear-gradient(to bottom, #f9fafb, #f3f4f6);">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider" style="border-bottom: 2px solid #e5e7eb;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <x-filament::icon icon="heroicon-o-user" class="w-4 h-4" />
                                            Contestant
                                        </div>
                                    </th>
                                    @foreach ($rounds as $round)
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider" style="border-bottom: 2px solid #e5e7eb;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                                <x-filament::icon icon="heroicon-o-arrow-path" class="w-4 h-4 text-primary-500" />
                                                <span>{{ $round->name }}</span>
                                            </div>
                                            <div class="text-xs text-gray-500 font-normal">
                                                <x-filament::badge size="sm" color="success">Max: {{ $round->max_score }}</x-filament::badge>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($contestants as $contestant)
                                    <tr class="hover:bg-primary-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap" style="background-color: #fafafa;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <x-filament::avatar 
                                                    src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=6366F1&background=E0E7FF"
                                                    size="md"
                                                />
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900">{{ $contestant->name }}</div>
                                                    @if ($contestant->description)
                                                        <div class="text-xs text-gray-500">{{ $contestant->description }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        @foreach ($rounds as $round)
                                            @php
                                                $key = $contestant->id . '_' . $round->id;
                                                $existingScore = $existingScores[$key] ?? null;
                                            @endphp
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-filament::input.wrapper>
                                                    <x-filament::input
                                                        type="number"
                                                        name="scores[{{ $key }}][score]"
                                                        x-model="scores['{{ $key }}'].score"
                                                        min="0"
                                                        max="{{ $round->max_score }}"
                                                        step="0.1"
                                                        value="{{ $existingScore?->score ?? '' }}"
                                                        style="width: 5rem;"
                                                    />
                                                </x-filament::input.wrapper>
                                                <input type="hidden" 
                                                       name="scores[{{ $key }}][contestant_id]" 
                                                       value="{{ $contestant->id }}">
                                                <input type="hidden" 
                                                       name="scores[{{ $key }}][round_id]" 
                                                       value="{{ $round->id }}">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endif
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <x-filament::button
                        type="submit"
                        size="lg"
                        icon="heroicon-o-check-circle">
                        Save Scores
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        <div style="margin-top: 1.5rem; text-align: center;">
            <x-filament::badge color="info" size="lg">
                <x-filament::icon icon="heroicon-o-information-circle" class="w-4 h-4" />
                Your scores are saved. You can return to this page anytime using your unique link.
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
                    // Initialize scores object based on mode
                    @if ($event->judging_type === 'criteria')
                        @foreach ($contestants as $contestant)
                            @foreach ($criterias as $criteria)
                                @php $key = $contestant->id . '_' . $criteria->id; @endphp
                                this.scores['{{ $key }}'] = { 
                                    score: {{ $existingScores[$key]->score ?? 0 }}
                                };
                            @endforeach
                        @endforeach
                    @elseif ($event->scoring_mode === 'boolean')
                        @foreach ($contestants as $contestant)
                            @foreach ($rounds as $round)
                                @php $key = $contestant->id . '_' . $round->id; @endphp
                                this.scores['{{ $key }}'] = { 
                                    is_correct: {{ $existingScores[$key]->is_correct ?? 'false' }}
                                };
                            @endforeach
                        @endforeach
                    @else
                        @foreach ($contestants as $contestant)
                            @foreach ($rounds as $round)
                                @php $key = $contestant->id . '_' . $round->id; @endphp
                                this.scores['{{ $key }}'] = { 
                                    score: {{ $existingScores[$key]->score ?? 0 }}
                                };
                            @endforeach
                        @endforeach
                    @endif
                }
            }
        }
    </script>
</body>
</html>
