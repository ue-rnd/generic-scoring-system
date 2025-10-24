@props([
    'icon',
    'label',
    'value',
    'color' => 'blue',
])

@php
    $colors = [
        'blue' => '#3b82f6',
        'green' => '#10b981',
        'purple' => '#8b5cf6',
        'orange' => '#f59e0b',
        'red' => '#ef4444',
    ];
    $colorValue = $colors[$color] ?? $colors['blue'];
@endphp

<div class="stat-card">
    @if($useFilament ?? true)
        <x-filament::icon :icon="$icon" class="stat-icon" :style="'color: ' . $colorValue" />
    @else
        <svg class="stat-icon" :style="'color: ' . $colorValue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {{ $iconPath ?? '' }}
        </svg>
    @endif
    <div class="stat-label">{{ $label }}</div>
    <div class="stat-value">{{ $value }}</div>
</div>
