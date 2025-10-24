<x-scoring-layout 
    :title="'Quiz Bee Event: ' . $event->name"
    :use-filament="false"
    :use-alpine="false">
    
    <style>
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.15s;
            width: 100%;
        }
        
        .btn-primary {
            background-color: #10b981;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #059669;
        }
        
        .btn-secondary {
            background-color: #f3f4f6;
            color: #4b5563;
        }
        
        .btn-secondary:hover {
            background-color: #e5e7eb;
            color: #111827;
        }
        
        .icon {
            width: 1.25rem;
            height: 1.25rem;
        }
        
        .icon-large {
            width: 4rem;
            height: 4rem;
        }
    </style>
    
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
        <div style="max-width: 600px; width: 100%;">
            <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem;">
                <div style="text-align: center;">
                    <!-- Academic Cap Icon -->
                    <svg class="icon-large" style="margin: 0 auto 1.5rem; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                    </svg>
                    
                    <h1 class="event-title" style="font-size: 1.875rem; font-weight: 700; color: #111827; margin: 0 0 1rem 0; justify-content: center;">
                        Quiz Bee Event
                    </h1>
                    
                    <h2 style="font-size: 1.25rem; font-weight: 600; color: #3b82f6; margin: 0 0 1.5rem 0;">
                        {{ $event->name }}
                    </h2>
                    
                    <div class="info-banner info" style="text-align: left;">
                        <svg class="info-banner-icon" style="color: #1e40af; flex-shrink: 0; margin-top: 0.125rem; width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="info-banner-content">
                            <p class="info-banner-title" style="color: #1e3a8a;">
                                This is a Quiz Bee Event
                            </p>
                            <p class="info-banner-text" style="color: #1e40af;">
                                Quiz bee events use a shared scoring system where all moderators work on the same scoresheet in real-time. 
                                Individual judge tokens are not used for quiz bee events.
                            </p>
                        </div>
                    </div>
                    
                    <p style="color: #6b7280; margin: 0 0 2rem 0;">
                        Please contact the event organizer to get the admin scoring URL, or use the public viewing link to see the results.
                    </p>
                    
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <a href="{{ route('public.view', $event->public_viewing_token) }}" class="btn btn-primary">
                            <!-- Chart Icon -->
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            View Public Results
                        </a>
                        
                        <a href="/" class="btn btn-secondary">
                            <!-- Home Icon -->
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-scoring-layout>
