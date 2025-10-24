<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Generic Scoring System' }}</title>
    
    @vite(['resources/css/app.css', 'resources/css/scoring-system.css', 'resources/js/app.js'])
    
    @if($useFilament ?? false)
        @filamentStyles
    @endif
    
    @if($useAlpine ?? true)
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endif
    
    {{ $head ?? '' }}
</head>
<body class="{{ $bodyClass ?? 'scoring-page-body' }}">
    <div class="{{ $containerClass ?? 'scoring-container' }}">
        {{ $slot }}
    </div>
    
    @if($useFilament ?? false)
        @filamentScripts
    @endif
    
    {{ $scripts ?? '' }}
</body>
</html>
