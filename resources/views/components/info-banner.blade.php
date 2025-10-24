@props([
    'type' => 'info', // info, success, warning, error
    'icon' => 'heroicon-o-information-circle',
    'title' => null,
    'dismissible' => false,
])

@php
    $typeClasses = [
        'info' => ['bg' => '#dbeafe', 'border' => '#3b82f6', 'text' => '#1e40af', 'title' => '#1e3a8a'],
        'success' => ['bg' => '#d1fae5', 'border' => '#10b981', 'text' => '#065f46', 'title' => '#064e3b'],
        'warning' => ['bg' => '#fef3c7', 'border' => '#f59e0b', 'text' => '#92400e', 'title' => '#78350f'],
        'error' => ['bg' => '#fee2e2', 'border' => '#ef4444', 'text' => '#991b1b', 'title' => '#7f1d1d'],
    ];
    $styles = $typeClasses[$type] ?? $typeClasses['info'];
@endphp

<div class="info-banner" style="background-color: {{ $styles['bg'] }}; border-color: {{ $styles['border'] }};">
    <div style="display: flex; align-items: start; gap: 0.75rem; flex: 1;">
        @if($useFilament ?? true)
            <x-filament::icon :icon="$icon" class="info-banner-icon" :style="'color: ' . $styles['text']" />
        @else
            <svg class="info-banner-icon" :style="'color: ' . $styles['text']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        @endif
        <div class="info-banner-content">
            @if($title)
                <p class="info-banner-title" :style="'color: ' . $styles['title']">{{ $title }}</p>
            @endif
            <div class="info-banner-text" :style="'color: ' . $styles['text']">
                {{ $slot }}
            </div>
        </div>
    </div>
    @if($dismissible)
        <button type="button" onclick="this.parentElement.remove()" style="color: {{ $styles['text'] }}; opacity: 0.7; cursor: pointer; background: none; border: none; padding: 0; font-size: 1.25rem;">
            ×
        </button>
    @endif
</div>
