<x-filament-panels::page>
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (min-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        .stat-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            border-radius: 0.5rem;
            background-color: rgba(59, 130, 246, 0.1);
        }
        .stat-icon {
            width: 1.5rem;
            height: 1.5rem;
            color: #3B82F6;
        }
        .stat-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6B7280;
            margin: 0;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }
        .section-spacing {
            margin-bottom: 1.5rem;
        }
        .link-container {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .link-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .link-input-container {
            flex: 1;
        }
        .link-description {
            font-size: 0.875rem;
            color: #6B7280;
            margin: 0;
        }
        .judge-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .judge-item {
            padding: 1rem;
            border: 1px solid #E5E7EB;
            border-radius: 0.5rem;
        }
        .judge-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        .judge-name {
            font-weight: 500;
            margin: 0;
        }
        .judge-scores {
            font-size: 0.875rem;
            color: #6B7280;
            margin: 0;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }
        .empty-icon {
            width: 3rem;
            height: 3rem;
            margin: 0 auto 0.5rem;
            color: #9CA3AF;
        }
        .empty-title {
            font-size: 0.875rem;
            font-weight: 500;
            margin: 0.5rem 0 0.25rem;
        }
        .empty-text {
            font-size: 0.875rem;
            color: #6B7280;
            margin: 0;
        }
        .heading-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>

    <!-- Event Statistics -->
    <div class="stats-grid">
        <x-filament::section>
            <div class="stat-content">
                <div class="stat-icon-wrapper">
                    <x-filament::icon 
                        icon="heroicon-o-users" 
                        style="width: 1.5rem; height: 1.5rem; color: #3B82F6;"
                    />
                </div>
                <div>
                    <p class="stat-label">Total Judges</p>
                    <p class="stat-value">{{ $statistics['total_judges'] ?? 0 }}</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="stat-content">
                <div class="stat-icon-wrapper">
                    <x-filament::icon 
                        icon="heroicon-o-user-group" 
                        style="width: 1.5rem; height: 1.5rem; color: #3B82F6;"
                    />
                </div>
                <div>
                    <p class="stat-label">Contestants</p>
                    <p class="stat-value">{{ $statistics['total_contestants'] ?? 0 }}</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="stat-content">
                <div class="stat-icon-wrapper">
                    <x-filament::icon 
                        icon="heroicon-o-clipboard-document-check" 
                        style="width: 1.5rem; height: 1.5rem; color: #3B82F6;"
                    />
                </div>
                <div>
                    <p class="stat-label">Completion</p>
                    <p class="stat-value">{{ $statistics['completion_percentage'] ?? 0 }}%</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="stat-content">
                <div class="stat-icon-wrapper">
                    <x-filament::icon 
                        icon="heroicon-o-eye" 
                        style="width: 1.5rem; height: 1.5rem; color: #3B82F6;"
                    />
                </div>
                <div>
                    <p class="stat-label">Status</p>
                    <p style="font-size: 1.125rem; font-weight: 600; color: {{ $record->is_active ? '#10B981' : '#6B7280' }}; margin: 0;">
                        {{ $record->is_active ? 'Active' : 'Inactive' }}
                    </p>
                </div>
            </div>
        </x-filament::section>
    </div>

    <!-- Public Viewing Link -->
    <x-filament::section class="section-spacing">
        <x-slot name="heading">
            <div class="heading-row">
                <span>Public Viewing Link</span>
                <x-filament::button
                    color="warning"
                    size="sm"
                    wire:click="regeneratePublicToken"
                    wire:confirm="Are you sure? This will invalidate the current public link."
                >
                    Regenerate Token
                </x-filament::button>
            </div>
        </x-slot>

        <div class="link-container">
            <div class="link-row">
                <div class="link-input-container">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="text"
                            readonly
                            value="{{ $publicViewingLink['url'] ?? '' }}"
                            style="font-family: monospace; font-size: 0.875rem;"
                        />
                    </x-filament::input.wrapper>
                </div>
                <x-filament::button
                    color="gray"
                    wire:click="copyToClipboard('{{ $publicViewingLink['url'] ?? '' }}')"
                >
                    <x-filament::icon icon="heroicon-o-clipboard" style="width: 1rem; height: 1rem;" />
                    Copy
                </x-filament::button>
                <x-filament::button
                    color="primary"
                    tag="a"
                    href="{{ $publicViewingLink['url'] ?? '#' }}"
                    target="_blank"
                >
                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" style="width: 1rem; height: 1rem;" />
                    Open
                </x-filament::button>
            </div>
            <p class="link-description">
                Share this link with your audience to allow them to view the live scoreboard.
            </p>
        </div>
    </x-filament::section>

    <!-- Admin Scoring Link (for Quiz Bee events) -->
    @if($record->isQuizBeeType())
    <x-filament::section class="section-spacing">
        <x-slot name="heading">
            Admin Scoring Link
        </x-slot>

        <div class="link-container">
            <div class="link-row">
                <div class="link-input-container">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="text"
                            readonly
                            value="{{ $record->admin_scoring_url }}"
                            style="font-family: monospace; font-size: 0.875rem;"
                        />
                    </x-filament::input.wrapper>
                </div>
                <x-filament::button
                    color="gray"
                    wire:click="copyToClipboard('{{ $record->admin_scoring_url }}')"
                >
                    <x-filament::icon icon="heroicon-o-clipboard" style="width: 1rem; height: 1rem;" />
                    Copy
                </x-filament::button>
                <x-filament::button
                    color="primary"
                    tag="a"
                    href="{{ $record->admin_scoring_url }}"
                    target="_blank"
                >
                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" style="width: 1rem; height: 1rem;" />
                    Open
                </x-filament::button>
            </div>
            <p class="link-description">
                Use this link to access the centralized quiz bee scoring interface.
            </p>
        </div>
    </x-filament::section>
    @endif

    <!-- Judge Links -->
    @if(!$record->isQuizBeeType())
    <x-filament::section>
        <x-slot name="heading">
            <div class="heading-row">
                <span>Judge Scoring Links</span>
                <x-filament::button
                    color="warning"
                    size="sm"
                    wire:click="regenerateJudgeTokens"
                    wire:confirm="Are you sure? This will invalidate all existing judge links."
                >
                    Regenerate All Tokens
                </x-filament::button>
            </div>
        </x-slot>

        @if(count($judgeLinks) === 0)
            <div class="empty-state">
                <x-filament::icon 
                    icon="heroicon-o-users" 
                    style="width: 3rem; height: 3rem; margin: 0 auto; color: #9CA3AF;"
                />
                <h3 class="empty-title">No judges</h3>
                <p class="empty-text">
                    Get started by adding judges to this event.
                </p>
            </div>
        @else
            <div class="judge-list">
                @foreach($judgeLinks as $judge)
                <div class="judge-item">
                    <div class="judge-header">
                        <div>
                            <h4 class="judge-name">
                                {{ $judge['name'] ?? 'Judge #' . $judge['id'] }}
                            </h4>
                            <p class="judge-scores">
                                Scores submitted: {{ $judge['scores_count'] ?? 0 }}
                            </p>
                        </div>
                        <x-filament::button
                            color="danger"
                            size="sm"
                            wire:click="deleteJudge({{ $judge['id'] }})"
                            wire:confirm="Are you sure you want to remove this judge?"
                        >
                            <x-filament::icon icon="heroicon-o-trash" style="width: 1rem; height: 1rem;" />
                            Remove
                        </x-filament::button>
                    </div>
                    <div class="link-row">
                        <div class="link-input-container">
                            <x-filament::input.wrapper>
                                <x-filament::input
                                    type="text"
                                    readonly
                                    value="{{ $judge['url'] }}"
                                    style="font-family: monospace; font-size: 0.875rem;"
                                />
                            </x-filament::input.wrapper>
                        </div>
                        <x-filament::button
                            color="gray"
                            wire:click="copyToClipboard('{{ $judge['url'] }}')"
                        >
                            <x-filament::icon icon="heroicon-o-clipboard" style="width: 1rem; height: 1rem;" />
                            Copy
                        </x-filament::button>
                        <x-filament::button
                            color="primary"
                            tag="a"
                            href="{{ $judge['url'] }}"
                            target="_blank"
                        >
                            <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" style="width: 1rem; height: 1rem;" />
                            Open
                        </x-filament::button>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('copy-to-clipboard', (event) => {
                const text = event.text;
                navigator.clipboard.writeText(text).then(() => {
                    console.log('Copied to clipboard');
                }).catch(err => {
                    console.error('Failed to copy:', err);
                });
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
