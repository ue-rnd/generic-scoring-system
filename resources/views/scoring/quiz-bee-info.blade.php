@extends('layouts.public')

@section('title', 'Quiz Bee Event - ' . $event->name)
@section('page-title', $event->name)

@section('content')
<div class="content-wrapper" style="max-width: 800px; padding-top: var(--spacing-4xl); padding-bottom: var(--spacing-4xl);">
    <div class="card" style="overflow: hidden; box-shadow: var(--shadow-xl);">
        <div class="card-gradient-header" style="text-align: center; padding: var(--spacing-2xl) var(--spacing-xl);">
            <svg style="margin: 0 auto; height: 4rem; width: 4rem; color: white;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            <h1 class="card-gradient-header-title" style="margin-top: var(--spacing-md); font-size: 1.5rem;">Quiz Bee Event</h1>
            <p class="card-gradient-header-subtitle" style="margin-top: var(--spacing-sm);">
                This is a quiz bee style event
            </p>
        </div>
        
        <div class="card-body" style="padding: var(--spacing-2xl) var(--spacing-xl);">
            <div style="text-align: center;">
                <div class="alert alert-info" style="margin-bottom: var(--spacing-md);">
                    <div style="display: flex; align-items: flex-start; text-align: left;">
                        <div style="flex-shrink: 0;">
                            <svg style="height: 1.25rem; width: 1.25rem; color: var(--primary);" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div style="margin-left: var(--spacing-sm); flex: 1;">
                            <h3 style="font-size: 0.875rem; font-weight: 600; color: var(--gray-900); margin: 0;">
                                Quiz Bee Scoring Information
                            </h3>
                            <div style="margin-top: var(--spacing-sm); font-size: 0.875rem; color: var(--gray-700);">
                                <p>Quiz bee events use a centralized scoring system where an administrator scores all contestants in real-time. Individual judge tokens are not used for this event type.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="border-top: 1px solid var(--gray-200); padding-top: var(--spacing-xl); margin-top: var(--spacing-md);">
                    <h3 style="font-size: 1.125rem; font-weight: 500; color: var(--gray-900); margin-bottom: var(--spacing-md);">Available Options:</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: var(--spacing-sm);">
                        <a href="{{ $event->admin_scoring_url }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                            <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Go to Admin Scoring Interface
                        </a>

                        <a href="{{ $event->public_viewing_url }}" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                            <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Public Scoreboard
                        </a>
                    </div>
                </div>

                <div style="margin-top: var(--spacing-xl); padding-top: var(--spacing-xl); border-top: 1px solid var(--gray-200);">
                    <h4 style="font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: var(--spacing-sm);">Event Details:</h4>
                    <dl class="grid grid-cols-2 gap-md" style="font-size: 0.875rem;">
                        <div>
                            <dt class="stat-label">Total Rounds</dt>
                            <dd class="stat-value" style="margin-top: var(--spacing-xs);">{{ $event->rounds->count() }}</dd>
                        </div>
                        <div>
                            <dt class="stat-label">Total Contestants</dt>
                            <dd class="stat-value" style="margin-top: var(--spacing-xs);">{{ $event->contestants->count() }}</dd>
                        </div>
                        <div>
                            <dt class="stat-label">Scoring Mode</dt>
                            <dd class="stat-value capitalize" style="margin-top: var(--spacing-xs);">{{ $event->scoring_mode }}</dd>
                        </div>
                        <div>
                            <dt class="stat-label">Status</dt>
                            <dd style="margin-top: var(--spacing-xs);">
                                <span class="badge {{ $event->is_active ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $event->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
