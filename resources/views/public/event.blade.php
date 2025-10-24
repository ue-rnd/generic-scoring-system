<x-scoring-layout 
    :title="$event->name . ' - Live Results'"
    body-class="scoring-page-body"
    container-class="public-view-container"
    :use-filament="false">
    
    <div x-data="liveResults()" x-init="init()">
        {{-- Header --}}
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="font-size: 3rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">{{ $event->name }}</h1>
            @if ($event->description)
                <p style="font-size: 1.25rem; color: #6b7280;">{{ $event->description }}</p>
            @endif
            <div class="live-indicator" style="margin-top: 1rem;">
                <span class="live-dot"></span>
                <span class="live-text">Live Updates</span>
                <span class="live-timestamp" x-text="'Updated: ' + lastUpdated"></span>
            </div>
        </div>

        {{-- Statistics --}}
        @if ($config['show_judge_progress'] ?? true)
            <div class="stats-grid section-spacing" style="background: white; padding: 2rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="stat-card">
                    <svg class="stat-icon" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <div class="stat-label">Contestants</div>
                    <div class="stat-value" x-text="statistics.total_contestants">{{ $statistics['total_contestants'] }}</div>
                </div>
                
                @if($statistics['is_quiz_bee'] ?? false)
                    {{-- Quiz Bee specific stats --}}
                    <div class="stat-card">
                        <svg class="stat-icon" style="color: #8b5cf6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="stat-label">Total Questions</div>
                        <div class="stat-value" x-text="statistics.total_questions">{{ $statistics['total_questions'] ?? 0 }}</div>
                    </div>
                @else
                    {{-- Judge-based stats --}}
                    <div class="stat-card">
                        <svg class="stat-icon" style="color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="stat-label">Active Judges</div>
                        <div class="stat-value" x-text="statistics.active_judges">{{ $statistics['active_judges'] }}</div>
                    </div>
                @endif
                
                <div class="stat-card">
                    <svg class="stat-icon" style="color: #8b5cf6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <div class="stat-label">{{ ($statistics['is_quiz_bee'] ?? false) ? 'Questions Answered' : 'Scores Submitted' }}</div>
                    <div class="stat-value" x-text="statistics.total_scores">{{ $statistics['total_scores'] }}</div>
                </div>
                <div class="stat-card">
                    <svg class="stat-icon" style="color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <div class="stat-label">Completion</div>
                    <div class="stat-value" x-text="statistics.completion_percentage.toFixed(1) + '%'">
                        {{ number_format($statistics['completion_percentage'], 1) }}%
                    </div>
                </div>
            </div>
        @endif

        {{-- Rankings/Leaderboard --}}
        @if ($config['show_rankings'] ?? true)
            <div class="rankings-container section-spacing">
                <h2 class="rankings-title">🏆 Current Rankings</h2>
                
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <template x-for="(result, index) in results" :key="result.contestant.id">
                        <div class="ranking-item"
                             :class="{
                                 'rank-1': result.rank === 1,
                                 'rank-2': result.rank === 2,
                                 'rank-3': result.rank === 3
                             }">
                            
                            {{-- Rank --}}
                            <div class="rank-display">
                                <template x-if="result.rank === 1">
                                    <div class="rank-emoji">🥇</div>
                                </template>
                                <template x-if="result.rank === 2">
                                    <div class="rank-emoji">🥈</div>
                                </template>
                                <template x-if="result.rank === 3">
                                    <div class="rank-emoji">🥉</div>
                                </template>
                                <template x-if="result.rank > 3">
                                    <div class="rank-number" x-text="'#' + result.rank"></div>
                                </template>
                            </div>
                            
                            {{-- Contestant Info --}}
                            <div class="contestant-info">
                                <div class="contestant-info-name" x-text="result.contestant.name"></div>
                                <template x-if="result.contestant.description">
                                    <div class="contestant-info-desc" x-text="result.contestant.description"></div>
                                </template>
                            </div>
                            
                            {{-- Score --}}
                            @if ($config['show_scores'] ?? false)
                                <div class="score-display">
                                    <div class="score-value" x-text="result.final_score.toFixed(2)"></div>
                                    <div class="score-label">points</div>
                                </div>
                            @endif
                        </div>
                    </template>
                </div>
            </div>
        @endif

        {{-- Judge Progress (if enabled) --}}
        @if ($config['show_judge_progress'] ?? true)
            <div class="rankings-container section-spacing">
                <h2 class="rankings-title">👨‍⚖️ Judge Progress</h2>
                
                <div class="stats-grid">
                    <template x-for="summary in judgeSummary" :key="summary.judge.id">
                        <div style="background-color: #f9fafb; padding: 1.5rem; border-radius: 0.5rem; border: 2px solid #e5e7eb;">
                            <div style="font-weight: 600; color: #111827; margin-bottom: 1rem;" x-show="config.show_judge_names" x-text="summary.judge.name || 'Judge'">
                                @if ($config['show_judge_names'] ?? false)
                                    Judge
                                @else
                                    Anonymous Judge
                                @endif
                            </div>
                            <div x-show="!config.show_judge_names" style="font-weight: 600; color: #111827; margin-bottom: 1rem;">Anonymous Judge</div>
                            
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span style="font-size: 0.875rem; color: #6b7280;">Completion</span>
                                    <span style="font-size: 0.875rem; font-weight: 600;" x-text="summary.completion_percentage.toFixed(1) + '%'"></span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar"
                                         :class="{
                                             'progress-complete': summary.completion_percentage === 100,
                                             'progress-partial': summary.completion_percentage >= 50 && summary.completion_percentage < 100,
                                             'progress-minimal': summary.completion_percentage < 50
                                         }"
                                         :style="'width: ' + summary.completion_percentage + '%'"></div>
                                </div>
                            </div>
                            <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.75rem;" x-text="summary.scores_count + ' scores submitted'"></div>
                        </div>
                    </template>
                </div>
            </div>
        @endif

        {{-- Footer --}}
        <div style="margin-top: 2rem; text-align: center; color: #6b7280;">
            <p style="font-size: 0.875rem;">Results update automatically every 5 seconds</p>
            <p style="font-size: 0.75rem; margin-top: 0.5rem;">
                {{ $event->judging_type === 'criteria' ? 'Criteria-based' : 'Rounds-based' }} Scoring | 
                {{ $event->scoring_mode === 'boolean' ? 'Correct/Incorrect Mode' : 'Manual Scoring Mode' }}
            </p>
        </div>
    </div>
    
    <x-slot name="scripts">
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
    </x-slot>
</x-scoring-layout>
