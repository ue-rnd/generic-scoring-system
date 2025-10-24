@props([
    'title',
    'description' => null,
    'icon' => 'heroicon-o-trophy',
    'actions' => null,
])

<div class="event-header section-spacing">
    <div style="flex: 1;">
        <div class="event-title">
            @if($useFilament ?? true)
                <x-filament::icon :icon="$icon" style="width: 2rem; height: 2rem; color: #3b82f6;" />
            @else
                <svg class="stat-icon" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            @endif
            <h1>{{ $title }}</h1>
        </div>
        
        @if($description)
            <p class="event-description">{{ $description }}</p>
        @endif
        
        {{ $badge ?? '' }}
    </div>
    
    @if($actions)
        <div>
            {{ $actions }}
        </div>
    @endif
</div>
