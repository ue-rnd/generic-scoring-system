<x-filament-panels::page>
    <div x-data="quizBeeScoring()">
        
        {{-- Quick Stats --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <x-filament::section>
                <div style="text-align: center;">
                    <x-filament::icon icon="heroicon-o-user-group" style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #8b5cf6;" />
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Contestants</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">{{ $record->contestants->count() }}</div>
                </div>
            </x-filament::section>
            
            <x-filament::section>
                <div style="text-align: center;">
                    <x-filament::icon icon="heroicon-o-arrow-path-rounded-square" style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #3b82f6;" />
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Rounds</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">{{ $record->rounds->count() }}</div>
                </div>
            </x-filament::section>
            
            <x-filament::section>
                <div style="text-align: center;">
                    <x-filament::icon icon="heroicon-o-calculator" style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #10b981;" />
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Scoring Mode</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 0.25rem; text-transform: capitalize;">{{ $record->scoring_mode }}</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div style="text-align: center;">
                    <x-filament::icon icon="heroicon-o-link" style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #f59e0b;" />
                    <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Admin Access</div>
                    <div style="margin-top: 0.5rem;">
                        <x-filament::button
                            tag="a"
                            href="{{ $record->admin_scoring_url }}"
                            target="_blank"
                            color="warning"
                            size="sm"
                            icon="heroicon-o-arrow-top-right-on-square">
                            Open Scoring
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Info Banner --}}
        <x-filament::section style="margin-bottom: 1.5rem; background-color: #dbeafe; border-color: #3b82f6;">
            <div style="display: flex; align-items: start; gap: 0.75rem;">
                <x-filament::icon icon="heroicon-o-information-circle" style="width: 1.5rem; height: 1.5rem; color: #1e40af; flex-shrink: 0; margin-top: 0.125rem;" />
                <div>
                    <p style="color: #1e3a8a; font-weight: 600; margin-bottom: 0.5rem;">Quiz Bee Scoring</p>
                    <p style="color: #1e40af; font-size: 0.875rem; line-height: 1.5;">
                        This event uses the <strong>{{ ucfirst($record->scoring_mode) }}</strong> scoring mode. 
                        Share the admin scoring URL with moderators to allow collaborative scoring. 
                        All moderators will see and edit the same scores in real-time.
                    </p>
                    <div style="margin-top: 1rem; padding: 0.75rem; background-color: white; border-radius: 0.375rem; border: 1px solid #93c5fd;">
                        <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Admin Scoring URL:</div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <code style="flex: 1; font-size: 0.875rem; color: #1e40af; font-family: monospace;">{{ $record->admin_scoring_url }}</code>
                            <x-filament::button
                                x-on:click="
                                    navigator.clipboard.writeText('{{ $record->admin_scoring_url }}');
                                    $tooltip('Copied to clipboard!', { timeout: 2000 });
                                "
                                color="primary"
                                size="xs"
                                icon="heroicon-o-clipboard-document">
                                Copy
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Rounds Management --}}
        @if($record->rounds->count() > 0)
            <x-filament::section>
                <x-slot name="heading">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <x-filament::icon icon="heroicon-o-queue-list" style="width: 1.5rem; height: 1.5rem;" />
                        <span>Rounds Configuration</span>
                    </div>
                </x-slot>

                <div style="display: grid; gap: 1rem;">
                    @foreach($record->rounds->sortBy('order') as $round)
                        <div style="padding: 1rem; background-color: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <x-filament::badge color="primary" size="lg">Round {{ $round->order }}</x-filament::badge>
                                        <h3 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin: 0;">{{ $round->name }}</h3>
                                    </div>
                                    @if($round->description)
                                        <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">{{ $round->description }}</p>
                                    @endif
                                </div>
                                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                                    <div>
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Questions</div>
                                        <x-filament::badge color="info" size="xl">{{ $round->total_questions }}</x-filament::badge>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Points/Question</div>
                                        <x-filament::badge color="success" size="xl">{{ $round->points_per_question }}</x-filament::badge>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">Max Score</div>
                                        <x-filament::badge color="warning" size="xl">{{ $round->max_score }}</x-filament::badge>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>

            {{-- Current Standings --}}
            @php
                $standings = $record->contestants->map(function($contestant) use ($record) {
                    $totalScore = \App\Models\Score::where('event_id', $record->id)
                        ->where('contestant_id', $contestant->id)
                        ->whereNotNull('round_id')
                        ->sum('score');
                    
                    return [
                        'contestant' => $contestant,
                        'total' => $totalScore,
                    ];
                })->sortByDesc('total')->values();
            @endphp

            @if($standings->sum('total') > 0)
                <x-filament::section style="margin-top: 1.5rem;">
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <x-filament::icon icon="heroicon-o-trophy" style="width: 1.5rem; height: 1.5rem;" />
                            <span>Current Standings</span>
                        </div>
                    </x-slot>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background: linear-gradient(to bottom, #f9fafb, #f3f4f6);">
                                <tr>
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 700; color: #374151; text-transform: uppercase; border-bottom: 2px solid #e5e7eb;">Rank</th>
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 700; color: #374151; text-transform: uppercase; border-bottom: 2px solid #e5e7eb;">Contestant</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 700; color: #374151; text-transform: uppercase; border-bottom: 2px solid #e5e7eb;">Total Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($standings as $index => $standing)
                                    <tr style="border-bottom: 1px solid #e5e7eb; {{ $index < 3 ? 'background-color: #fef3c7;' : '' }}">
                                        <td style="padding: 1rem;">
                                            <div style="display: flex; align-items: center; justify-content: center; min-width: 2.5rem;">
                                                @if($index === 0)
                                                    <span style="font-size: 1.5rem;">🥇</span>
                                                @elseif($index === 1)
                                                    <span style="font-size: 1.5rem;">🥈</span>
                                                @elseif($index === 2)
                                                    <span style="font-size: 1.5rem;">🥉</span>
                                                @else
                                                    <x-filament::badge color="gray" size="lg">#{{ $index + 1 }}</x-filament::badge>
                                                @endif
                                            </div>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <x-filament::avatar 
                                                    src="https://ui-avatars.com/api/?name={{ urlencode($standing['contestant']->name) }}&color=3b82f6&background=dbeafe"
                                                    size="md"
                                                />
                                                <div>
                                                    <div style="font-weight: 600; color: #111827;">{{ $standing['contestant']->name }}</div>
                                                    @if($standing['contestant']->description)
                                                        <div style="font-size: 0.75rem; color: #6b7280;">{{ $standing['contestant']->description }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 1rem; text-align: center;">
                                            <x-filament::badge color="primary" size="xl">
                                                {{ number_format($standing['total'], 2) }} points
                                            </x-filament::badge>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @endif
        @else
            <x-filament::section>
                <div style="text-align: center; padding: 3rem;">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" style="width: 3rem; height: 3rem; margin: 0 auto 1rem; color: #f59e0b;" />
                    <p style="color: #6b7280; font-size: 1.125rem; font-weight: 600;">No rounds configured</p>
                    <p style="color: #9ca3af; margin-top: 0.5rem;">Please add rounds to this event before scoring.</p>
                    <div style="margin-top: 1.5rem;">
                        <x-filament::button
                            tag="a"
                            :href="$this->getResource()::getUrl('edit', ['record' => $record])"
                            color="primary"
                            icon="heroicon-o-plus-circle">
                            Configure Rounds
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>

    @push('scripts')
        <script>
            function quizBeeScoring() {
                return {
                    // Add any interactive functionality here
                }
            }
        </script>
    @endpush
</x-filament-panels::page>
