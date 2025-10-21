<x-filament-panels::page>
    {{-- Event Statistics --}}
    <x-filament::section>
        <x-slot name="heading">
            Event Statistics
        </x-slot>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 0.5rem;">Total Judges</div>
                <div style="font-size: 2rem; font-weight: bold; margin-bottom: 0.25rem;">{{ $statistics['total_judges'] }}</div>
                <div style="font-size: 0.75rem; color: var(--gray-500);">{{ $statistics['active_judges'] }} active, {{ $statistics['pending_judges'] }} pending</div>
            </div>
            
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 0.5rem;">Contestants</div>
                <div style="font-size: 2rem; font-weight: bold;">{{ $statistics['total_contestants'] }}</div>
            </div>
            
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 0.5rem;">Completion</div>
                <div style="font-size: 2rem; font-weight: bold; margin-bottom: 0.25rem;">{{ number_format($statistics['completion_percentage'], 1) }}%</div>
                <div style="font-size: 0.75rem; color: var(--gray-500);">{{ $statistics['total_scores'] }} scores submitted</div>
            </div>
        </div>
    </x-filament::section>

    {{-- Public Viewing Link --}}
    <x-filament::section>
        <x-slot name="heading">
            Public Viewing Link
        </x-slot>
        
        <x-slot name="description">
            Share this link with your audience to view live results
        </x-slot>
        
        <x-slot name="headerEnd">
            <x-filament::button
                color="warning"
                size="sm"
                wire:click="regeneratePublicToken"
                wire:confirm="Are you sure? This will invalidate the current public link."
            >
                Regenerate Token
            </x-filament::button>
        </x-slot>
        
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-start;">
            <div style="flex: 1; min-width: 300px;">
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="text"
                        value="{{ $publicViewingLink['url'] }}"
                        readonly
                    />
                </x-filament::input.wrapper>
            </div>
            
            <x-filament::button
                wire:click="copyToClipboard('{{ $publicViewingLink['url'] }}')"
                outlined
            >
                <x-filament::icon
                    icon="heroicon-m-clipboard-document"
                    class="h-5 w-5"
                />
                Copy Link
            </x-filament::button>
        </div>
        
        <div style="margin-top: 1.5rem;">
            <img 
                src="{{ $publicViewingLink['qr_code_url'] }}" 
                alt="Public Viewing QR Code" 
                style="width: 100%; height: auto; max-width: 200px; border-radius: 0.5rem; border: 1px solid var(--gray-200);"
            />
        </div>
    </x-filament::section>

    {{-- Judge Links --}}
    <x-filament::section>
        <x-slot name="heading">
            Judge Scoring Links
        </x-slot>
        
        <x-slot name="description">
            Individual scoring links for each judge with unique access tokens
        </x-slot>
        
        <x-slot name="headerEnd">
            <x-filament::button
                color="warning"
                size="sm"
                wire:click="regenerateJudgeTokens"
                wire:confirm="Are you sure? This will invalidate all current judge links."
            >
                Regenerate All Tokens
            </x-filament::button>
        </x-slot>
        
        @if (count($judgeLinks) === 0)
            <div style="text-align: center; padding: 3rem 0;">
                <x-filament::icon
                    icon="heroicon-o-user-group"
                    style="width: 3rem; height: 3rem; margin: 0 auto 1rem; color: var(--gray-400);"
                />
                <div style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">No judges added yet</div>
                <div style="font-size: 0.875rem; color: var(--gray-500);">Click "Add Judges" button above to get started</div>
            </div>
        @else
            @foreach ($judgeLinks as $judge)
                <x-filament::card style="margin-bottom: 1rem;">
                    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                        <div style="flex-shrink: 0;">
                            <img 
                                src="{{ $judge['qr_code_url'] }}" 
                                alt="QR Code for {{ $judge['judge_name'] }}" 
                                style="width: 120px; height: 120px; border-radius: 0.5rem; border: 1px solid var(--gray-200);"
                            />
                        </div>
                        
                        <div style="flex: 1; min-width: 300px;">
                            <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">
                                {{ $judge['judge_name'] }}
                            </div>
                            
                            <div style="margin-bottom: 1rem;">
                                <x-filament::badge 
                                    :color="$judge['status'] === 'accepted' ? 'success' : 'gray'"
                                >
                                    {{ ucfirst($judge['status']) }}
                                </x-filament::badge>
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 250px;">
                                    <x-filament::input.wrapper>
                                        <x-filament::input
                                            type="text"
                                            value="{{ $judge['url'] }}"
                                            readonly
                                            style="font-family: ui-monospace, monospace; font-size: 0.875rem;"
                                        />
                                    </x-filament::input.wrapper>
                                </div>
                                
                                <x-filament::button
                                    size="sm"
                                    wire:click="copyToClipboard('{{ $judge['url'] }}')"
                                    outlined
                                >
                                    <x-filament::icon
                                        icon="heroicon-m-clipboard-document"
                                        class="h-4 w-4"
                                    />
                                    Copy
                                </x-filament::button>
                            </div>
                        </div>
                        
                        <div style="flex-shrink: 0; display: flex; align-items: flex-start;">
                            <x-filament::button
                                color="danger"
                                size="sm"
                                wire:click="deleteJudge({{ $judge['id'] }})"
                                wire:confirm="Are you sure you want to remove this judge?"
                            >
                                <x-filament::icon
                                    icon="heroicon-m-trash"
                                    class="h-4 w-4"
                                />
                                Remove
                            </x-filament::button>
                        </div>
                    </div>
                </x-filament::card>
            @endforeach
        @endif
    </x-filament::section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('copy-to-clipboard', event => {
                navigator.clipboard.writeText(event.detail.text);
            });
        });
    </script>
    @endpush
</x-filament-panels::page>