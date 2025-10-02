<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Score Event: {{ $event->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->name }}</h1>
                <p class="text-gray-600 mt-2">{{ $event->description }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('judge.events') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Back to Events</a>
                <a href="{{ route('judge.results', $event) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">View Results</a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Scoring Interface</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-blue-50 p-3 rounded">
                        <span class="font-medium">Event Type:</span> {{ ucfirst($event->judging_type) }}-based
                    </div>
                    <div class="bg-blue-50 p-3 rounded">
                        <span class="font-medium">Contestants:</span> {{ $contestants->count() }}
                    </div>
                    @if ($event->judging_type === 'criteria')
                        <div class="bg-blue-50 p-3 rounded">
                            <span class="font-medium">Criteria:</span> {{ $criterias->count() }}
                        </div>
                    @else
                        <div class="bg-blue-50 p-3 rounded">
                            <span class="font-medium">Rounds:</span> {{ $rounds->count() }}
                        </div>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('judge.scores.store', $event) }}" x-data="scoringForm()">
                @csrf
                
                @if ($event->judging_type === 'criteria')
                    <!-- Criteria-based scoring -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contestant</th>
                                    @foreach ($criterias as $criteria)
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $criteria->name }}
                                            <div class="text-xs text-gray-400">Max: {{ $criteria->max_score }}</div>
                                            <div class="text-xs text-gray-400">Weight: {{ $criteria->weight }}</div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($contestants as $contestant)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $contestant->name }}</div>
                                            @if ($contestant->description)
                                                <div class="text-sm text-gray-500">{{ $contestant->description }}</div>
                                            @endif
                                        </td>
                                        @foreach ($criterias as $criteria)
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" 
                                                       name="scores[{{ $contestant->id }}_{{ $criteria->id }}][score]"
                                                       x-model="scores['{{ $contestant->id }}_{{ $criteria->id }}'].score"
                                                       min="0" 
                                                       max="{{ $criteria->max_score }}" 
                                                       step="0.1"
                                                       class="w-20 px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                                <input type="hidden" 
                                                       name="scores[{{ $contestant->id }}_{{ $criteria->id }}][contestant_id]" 
                                                       value="{{ $contestant->id }}">
                                                <input type="hidden" 
                                                       name="scores[{{ $contestant->id }}_{{ $criteria->id }}][criteria_id]" 
                                                       value="{{ $criteria->id }}">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Rounds-based scoring -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contestant</th>
                                    @foreach ($rounds as $round)
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $round->name }}
                                            <div class="text-xs text-gray-400">Max: {{ $round->max_score }}</div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($contestants as $contestant)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $contestant->name }}</div>
                                            @if ($contestant->description)
                                                <div class="text-sm text-gray-500">{{ $contestant->description }}</div>
                                            @endif
                                        </td>
                                        @foreach ($rounds as $round)
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" 
                                                       name="scores[{{ $contestant->id }}_{{ $round->id }}][score]"
                                                       x-model="scores['{{ $contestant->id }}_{{ $round->id }}'].score"
                                                       min="0" 
                                                       max="{{ $round->max_score }}" 
                                                       step="0.1"
                                                       class="w-20 px-2 py-1 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                                <input type="hidden" 
                                                       name="scores[{{ $contestant->id }}_{{ $round->id }}][contestant_id]" 
                                                       value="{{ $contestant->id }}">
                                                <input type="hidden" 
                                                       name="scores[{{ $contestant->id }}_{{ $round->id }}][round_id]" 
                                                       value="{{ $round->id }}">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" 
                            @click="saveDraft()" 
                            class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">
                        Save Draft
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        Submit Scores
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function scoringForm() {
            return {
                scores: {},
                
                init() {
                    this.loadExistingScores();
                },
                
                async loadExistingScores() {
                    try {
                        const response = await fetch('{{ route("judge.scores.get", $event) }}');
                        const data = await response.json();
                        
                        // Initialize scores object
                        @if ($event->judging_type === 'criteria')
                            @foreach ($contestants as $contestant)
                                @foreach ($criterias as $criteria)
                                    this.scores['{{ $contestant->id }}_{{ $criteria->id }}'] = { score: 0 };
                                @endforeach
                            @endforeach
                        @else
                            @foreach ($contestants as $contestant)
                                @foreach ($rounds as $round)
                                    this.scores['{{ $contestant->id }}_{{ $round->id }}'] = { score: 0 };
                                @endforeach
                            @endforeach
                        @endif
                        
                        // Load existing scores
                        Object.keys(data).forEach(key => {
                            if (this.scores[key]) {
                                this.scores[key].score = data[key].score || 0;
                            }
                        });
                    } catch (error) {
                        console.error('Error loading scores:', error);
                    }
                },
                
                async saveDraft() {
                    // This would save scores without submitting
                    alert('Draft saved! (This feature can be implemented with AJAX)');
                }
            }
        }
    </script>
</body>
</html>


