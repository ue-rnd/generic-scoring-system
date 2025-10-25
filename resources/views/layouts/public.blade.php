<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Generic Scoring System'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Shared Styles -->
    <link rel="stylesheet" href="{{ asset('css/scoring-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/scoring-public.css') }}">
    
    @stack('styles')
</head>
<body>
    <div class="main-container">
        @if(!isset($hideHeader) || !$hideHeader)
        <header class="header">
            <div class="header-content">
                <div class="header-title-section">
                    <svg class="header-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <div>
                        <h1 class="header-title">@yield('page-title', config('app.name', 'Scoring System'))</h1>
                        @if(isset($subtitle))
                        <p class="header-subtitle">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
                <div class="header-actions">
                    @yield('header-actions')
                </div>
            </div>
        </header>
        @endif

        <main class="main-content">
            @if(session('success'))
            <div class="content-wrapper mb-md">
                <div class="alert alert-success">
                    <svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p><strong>{{ session('success') }}</strong></p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="content-wrapper mb-md">
                <div class="alert alert-error">
                    <svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z" clip-rule="evenodd" />
                    </svg>
                    <p><strong>{{ session('error') }}</strong></p>
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="content-wrapper mb-md">
                <div class="alert alert-error">
                    <svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p><strong>There were errors with your submission:</strong></p>
                        <ul style="list-style: disc; margin-left: 1.25rem; margin-top: 0.5rem;">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            @yield('content')
        </main>

        @if(!isset($hideFooter) || !$hideFooter)
        <footer class="footer">
            <div class="footer-content">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Scoring System') }}. All rights reserved.</p>
            </div>
        </footer>
        @endif
    </div>

    @stack('scripts')
</body>
</html>
