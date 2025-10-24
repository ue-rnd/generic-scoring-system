@props([
    'rank',
    'contestant',
    'score' => null,
    'showScore' => true,
])

@php
    $isTopThree = $rank <= 3;
    $bgClass = match($rank) {
        1 => 'rank-1',
        2 => 'rank-2',
        3 => 'rank-3',
        default => '',
    };
    $emoji = match($rank) {
        1 => '🥇',
        2 => '🥈',
        3 => '🥉',
        default => null,
    };
@endphp

<div class="ranking-item {{ $bgClass }}">
    <div class="rank-display">
        @if($emoji)
            <div class="rank-emoji">{{ $emoji }}</div>
        @else
            <div class="rank-number">#{{ $rank }}</div>
        @endif
    </div>
    
    <div class="contestant-info">
        <div class="contestant-info-name">{{ $contestant->name }}</div>
        @if($contestant->description)
            <div class="contestant-info-desc">{{ $contestant->description }}</div>
        @endif
    </div>
    
    @if($showScore && $score !== null)
        <div class="score-display">
            <div class="score-value">{{ number_format($score, 2) }}</div>
            <div class="score-label">points</div>
        </div>
    @endif
</div>
