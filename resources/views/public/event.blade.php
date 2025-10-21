<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->name }} - Live Results</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div x-data="liveResults()" x-init="init()" class="container mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold text-gray-900 mb-2">{{ $event->name }}</h1>
            @if ($event->description)
                <p class="text-xl text-gray-600">{{ $event->description }}</p>
            @endif
            <div class="mt-4 inline-flex items-center px-4 py-2 bg-white rounded-full shadow-md">
                <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse mr-2"></span>
                <span class="text-sm font-medium text-gray-700">Live Updates</span>
                <span class="ml-2 text-xs text-gray-500" x-text="'Updated: ' + lastUpdated"></span>
            </div>
        </div>

        {{-- Statistics --}}
        @if ($config['show_judge_progress'] ?? true)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600" x-text="statistics.total_contestants">{{ $statistics['total_contestants'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Contestants</div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-3xl font-bold text-green-600" x-text="statistics.active_judges">{{ $statistics['active_judges'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Active Judges</div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-3xl font-bold text-purple-600" x-text="statistics.total_scores">{{ $statistics['total_scores'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Scores Submitted</div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-3xl font-bold text-orange-600" x-text="statistics.completion_percentage.toFixed(1) + '%'">
                        {{ number_format($statistics['completion_percentage'], 1) }}%
                    </div>
                    <div class="text-sm text-gray-600 mt-1">Completion</div>
                </div>
            </div>
        @endif

        {{-- Rankings/Leaderboard --}}
        @if ($config['show_rankings'] ?? true)
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <h2 class="text-3xl font-bold mb-6 text-center">🏆 Current Rankings</h2>
                
                <div class="space-y-4">
                    <template x-for="(result, index) in results" :key="result.contestant.id">
                        <div class="flex items-center p-4 rounded-lg transition-all duration-300"
                             :class="{
                                 'bg-gradient-to-r from-yellow-200 to-yellow-100 transform scale-105': result.rank === 1,
                                 'bg-gradient-to-r from-gray-200 to-gray-100': result.rank === 2,
                                 'bg-gradient-to-r from-orange-200 to-orange-100': result.rank === 3,
                                 'bg-gray-50 hover:bg-gray-100': result.rank > 3
                             }">
                            
                            {{-- Rank --}}
                            <div class="w-16 text-center">
                                <template x-if="result.rank === 1">
                                    <span class="text-4xl">🥇</span>
                                </template>
                                <template x-if="result.rank === 2">
                                    <span class="text-4xl">🥈</span>
                                </template>
                                <template x-if="result.rank === 3">
                                    <span class="text-4xl">🥉</span>
                                </template>
                                <template x-if="result.rank > 3">
                                    <span class="text-2xl font-bold text-gray-600" x-text="'#' + result.rank"></span>
                                </template>
                            </div>
                            
                            {{-- Contestant Info --}}
                            <div class="flex-1 ml-4">
                                <div class="font-bold text-xl" x-text="result.contestant.name"></div>
                                <template x-if="result.contestant.description">
                                    <div class="text-sm text-gray-600" x-text="result.contestant.description"></div>
                                </template>
                            </div>
                            
                            {{-- Score --}}
                            @if ($config['show_scores'] ?? false)
                                <div class="text-right">
                                    <div class="text-3xl font-bold text-blue-600" x-text="result.final_score.toFixed(2)"></div>
                                    <div class="text-xs text-gray-500">points</div>
                                </div>
                            @endif
                        </div>
                    </template>
                </div>
            </div>
        @endif

        {{-- Judge Progress (if enabled) --}}
        @if ($config['show_judge_progress'] ?? true)
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-3xl font-bold mb-6 text-center">👨‍⚖️ Judge Progress</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="summary in judgeSummary" :key="summary.judge.id">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="font-medium text-gray-900" x-show="config.show_judge_names" x-text="summary.judge.name || 'Judge'">
                                @if ($config['show_judge_names'] ?? false)
                                    Judge
                                @else
                                    Anonymous Judge
                                @endif
                            </div>
                            <div x-show="!config.show_judge_names" class="font-medium text-gray-900">Anonymous Judge</div>
                            
                            <div class="mt-2">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm text-gray-600">Completion</span>
                                    <span class="text-sm font-semibold" x-text="summary.completion_percentage.toFixed(1) + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                                         :style="'width: ' + summary.completion_percentage + '%'"></div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 mt-2" x-text="summary.scores_count + ' scores submitted'"></div>
                        </div>
                    </template>
                </div>
            </div>
        @endif

        {{-- Footer --}}
        <div class="mt-8 text-center text-gray-600">
            <p class="text-sm">Results update automatically every 5 seconds</p>
            <p class="text-xs mt-2">{{ $event->judging_type === 'criteria' ? 'Criteria-based' : 'Rounds-based' }} Scoring | 
                {{ $event->scoring_mode === 'boolean' ? 'Correct/Incorrect Mode' : 'Manual Scoring Mode' }}
            </p>
        </div>
    </div>

    <script>
        function liveResults() {
            return {
                results: @json($results),
                judgeSummary: @json($judgeSummary),
                statistics: @json($statistics),
                config: @json($config),
                lastUpdated: 'Just now',
                
                init() {
                    this.startLiveUpdates();
                },
                
                startLiveUpdates() {
                    setInterval(() => {
                        this.fetchLiveData();
                    }, 5000); // Update every 5 seconds
                },
                
                async fetchLiveData() {
                    try {
                        const response = await fetch('{{ route("public.live", ["token" => $event->public_viewing_token]) }}');
                        const data = await response.json();
                        
                        this.results = data.results;
                        this.judgeSummary = data.judge_summary;
                        this.statistics = data.statistics;
                        this.lastUpdated = new Date().toLocaleTimeString();
                    } catch (error) {
                        console.error('Error fetching live data:', error);
                    }
                }
            }
        }
    </script>
</body>
</html>
