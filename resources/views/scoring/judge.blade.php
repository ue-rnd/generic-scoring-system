<!DOCTYPE html><!DOCTYPE html>

<html lang="en" class="fi"><html lang="en" class="fi">

<head><head>

    <meta charset="UTF-8">    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Score Event: {{ $event->name }}</title>    <title>Score Event: {{ $event->name }}</title>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        

    @filamentStyles    @filamentStyles

        

    <style>    <style>

        body {        body {

            margin: 0;            margin: 0;

            padding: 2rem 1rem;            padding: 2rem 1rem;

            min-height: 100vh;            min-height: 100vh;

        }        }

                

        .scoring-container {        .scoring-container {

            max-width: 1400px;            max-width: 1400px;

            margin: 0 auto;            margin: 0 auto;

        }        }

                

        .section-spacing {        .section-spacing {

            margin-bottom: 1.5rem;            margin-bottom: 1.5rem;

        }        }

                

        .header-flex {        .header-flex {

            display: flex;            display: flex;

            justify-content: space-between;            justify-content: space-between;

            align-items: flex-start;            align-items: flex-start;

            flex-wrap: wrap;            flex-wrap: wrap;

            gap: 1rem;            gap: 1rem;

        }        }

                

        .stats-grid {        .stats-grid {

            display: grid;            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));

            gap: 1rem;            gap: 1rem;

        }        }

                

        .stat-card {        .stat-card {

            text-align: center;            text-align: center;

            padding: 1.5rem;            padding: 1.5rem;

        }        }

                

        .stat-icon {        .stat-icon {

            width: 2rem;            width: 2rem;

            height: 2rem;            height: 2rem;

            margin: 0 auto 0.5rem;            margin: 0 auto 0.5rem;

        }        }

                

        .stat-label {        .stat-label {

            font-size: 0.75rem;            font-size: 0.75rem;

            text-transform: uppercase;            text-transform: uppercase;

            letter-spacing: 0.05em;            letter-spacing: 0.05em;

            opacity: 0.7;            opacity: 0.7;

        }        }

                

        .stat-value {        .stat-value {

            font-size: 1.5rem;            font-size: 1.5rem;

            font-weight: bold;            font-weight: bold;

            margin-top: 0.25rem;            margin-top: 0.25rem;

        }        }

                

        .scoring-table {        .scoring-table {

            width: 100%;            width: 100%;

            overflow-x: auto;            overflow-x: auto;

        }        }

                

        .scoring-table table {        .scoring-table table {

            width: 100%;            width: 100%;

            border-collapse: collapse;            border-collapse: collapse;

        }        }

                

        .scoring-table th,        .scoring-table th,

        .scoring-table td {        .scoring-table td {

            padding: 1rem 1.5rem;            padding: 1rem 1.5rem;

            text-align: left;            text-align: left;

            border-bottom: 1px solid rgba(0,0,0,0.05);            border-bottom: 1px solid rgba(0,0,0,0.05);

        }        }

                

        .scoring-table thead tr {        .scoring-table thead tr {

            background-image: linear-gradient(to bottom, rgba(0,0,0,0.02), rgba(0,0,0,0.04));            background-image: linear-gradient(to bottom, rgba(0,0,0,0.02), rgba(0,0,0,0.04));

            border-bottom: 2px solid rgba(0,0,0,0.1);            border-bottom: 2px solid rgba(0,0,0,0.1);

        }        }

                

        .scoring-table th {        .scoring-table th {

            font-weight: bold;            font-weight: bold;

            font-size: 0.75rem;            font-size: 0.75rem;

            text-transform: uppercase;            text-transform: uppercase;

            letter-spacing: 0.05em;            letter-spacing: 0.05em;

        }        }

                

        .scoring-table tbody tr:nth-child(odd) td:first-child {        .scoring-table tbody tr:nth-child(odd) td:first-child {

            background-color: rgba(0,0,0,0.02);            background-color: rgba(0,0,0,0.02);

        }        }

                

        .contestant-cell {        .contestant-cell {

            display: flex;            display: flex;

            align-items: center;            align-items: center;

            gap: 0.75rem;            gap: 0.75rem;

        }        }

                

        .contestant-name {        .contestant-name {

            font-weight: bold;            font-weight: bold;

            font-size: 0.875rem;            font-size: 0.875rem;

        }        }

                

        .contestant-desc {        .contestant-desc {

            font-size: 0.75rem;            font-size: 0.75rem;

            opacity: 0.7;            opacity: 0.7;

            margin-top: 0.125rem;            margin-top: 0.125rem;

        }        }

                

        .score-input-wrapper {        .score-input-wrapper {

            max-width: 5rem;            max-width: 5rem;

        }        }

                

        .checkbox-label {        .checkbox-label {

            display: flex;            display: flex;

            align-items: center;            align-items: center;

            gap: 0.75rem;            gap: 0.75rem;

            cursor: pointer;            cursor: pointer;

            padding: 0.5rem;            padding: 0.5rem;

            border-radius: 0.375rem;            border-radius: 0.375rem;

            transition: background-color 0.15s;            transition: background-color 0.15s;

        }        }

                

        .checkbox-label:hover {        .checkbox-label:hover {

            background-color: rgba(0,0,0,0.05);            background-color: rgba(0,0,0,0.05);

        }        }

                

        .form-actions {        .form-actions {

            display: flex;            display: flex;

            justify-content: flex-end;            justify-content: flex-end;

            gap: 1rem;            gap: 1rem;

            margin-top: 1.5rem;            margin-top: 1.5rem;

            padding-top: 1.5rem;            padding-top: 1.5rem;

            border-top: 1px solid rgba(0,0,0,0.1);            border-top: 1px solid rgba(0,0,0,0.1);

        }        }

                

        .info-banner {        .info-banner {

            text-align: center;            text-align: center;

            margin-top: 1.5rem;            margin-top: 1.5rem;

        }        }

                

        .badge-group {        .badge-group {

            display: flex;            display: flex;

            gap: 0.5rem;            gap: 0.5rem;

            font-size: 0.75rem;            font-size: 0.75rem;

            font-weight: normal;            font-weight: normal;

            flex-wrap: wrap;            flex-wrap: wrap;

        }        }

    </style>    </style>

</head></head>

<body class="fi-body"><body class="fi-body">

    <div class="scoring-container">    <div class="scoring-container">

        {{-- Event Header --}}        {{-- Event Header --}}

        <x-filament::section class="section-spacing">        <x-filament::section class="section-spacing">

            <div class="header-flex">            <div class="header-flex">

                <div>                <div>

                    <h1 style="font-size: 1.875rem; font-weight: bold; margin-bottom: 0.5rem;">{{ $event->name }}</h1>                    <h1 style="font-size: 1.875rem; font-weight: bold; margin-bottom: 0.5rem;">{{ $event->name }}</h1>

                    @if($event->description)                    @if($event->description)

                        <p style="opacity: 0.8; margin-bottom: 0.75rem;">{{ $event->description }}</p>                        <p style="opacity: 0.8; margin-bottom: 0.75rem;">{{ $event->description }}</p>

                    @endif                    @endif

                    <x-filament::badge color="info">                    <x-filament::badge color="info">

                        <div style="display: flex; align-items: center; gap: 0.5rem;">                        <div style="display: flex; align-items: center; gap: 0.5rem;">

                            <x-filament::icon icon="heroicon-o-user-circle" style="width: 1rem; height: 1rem;" />                            <x-filament::icon icon="heroicon-o-user-circle" style="width: 1rem; height: 1rem;" />

                            <span>Scoring as: {{ $judgeName }}</span>                            <span>Scoring as: {{ $judgeName }}</span>

                        </div>                        </div>

                    </x-filament::badge>                    </x-filament::badge>

                </div>                </div>

                <div>                <div>

                    <x-filament::button                     <x-filament::button 

                        tag="a"                         tag="a" 

                        href="{{ route('score.results', $token) }}"                         href="{{ route('score.results', $token) }}" 

                        color="success"                        color="success"

                        icon="heroicon-o-presentation-chart-line">                        icon="heroicon-o-presentation-chart-line">

                        View Results                        View Results

                    </x-filament::button>                    </x-filament::button>

                </div>                </div>

            </div>            </div>

        </x-filament::section>        </x-filament::section>



        {{-- Success Message --}}        {{-- Success Message --}}

        @if (session('success'))        @if (session('success'))

            <x-filament::section class="section-spacing" style="background-color: rgba(16, 185, 129, 0.1); border-color: rgb(16, 185, 129);">            <x-filament::section class="section-spacing" style="background-color: rgba(16, 185, 129, 0.1); border-color: rgb(16, 185, 129);">

                <div style="display: flex; align-items: center; gap: 0.75rem;">                <div style="display: flex; align-items: center; gap: 0.75rem;">

                    <x-filament::icon icon="heroicon-o-check-circle" style="width: 1.5rem; height: 1.5rem; color: rgb(16, 185, 129);" />                    <x-filament::icon icon="heroicon-o-check-circle" style="width: 1.5rem; height: 1.5rem; color: rgb(16, 185, 129);" />

                    <span style="font-weight: 500;">{{ session('success') }}</span>                    <span style="font-weight: 500;">{{ session('success') }}</span>

                </div>                </div>

            </x-filament::section>            </x-filament::section>

        @endif        @endif



        {{-- Error Messages --}}        {{-- Error Messages --}}

        @if ($errors->any())        @if ($errors->any())

            <x-filament::section class="section-spacing" style="background-color: rgba(239, 68, 68, 0.1); border-color: rgb(239, 68, 68);">            <x-filament::section class="section-spacing" style="background-color: rgba(239, 68, 68, 0.1); border-color: rgb(239, 68, 68);">

                <div style="display: flex; align-items: start; gap: 0.75rem;">                <div style="display: flex; align-items: start; gap: 0.75rem;">

                    <x-filament::icon icon="heroicon-o-x-circle" style="width: 1.5rem; height: 1.5rem; color: rgb(239, 68, 68); flex-shrink: 0;" />                    <x-filament::icon icon="heroicon-o-x-circle" style="width: 1.5rem; height: 1.5rem; color: rgb(239, 68, 68); flex-shrink: 0;" />

                    <div style="flex: 1;">                    <div style="flex: 1;">

                        <p style="font-weight: 500; margin-bottom: 0.5rem;">Please correct the following errors:</p>                        <p style="font-weight: 500; margin-bottom: 0.5rem;">Please correct the following errors:</p>

                        <ul style="list-style-type: disc; padding-left: 1.5rem;">                        <ul style="list-style-type: disc; padding-left: 1.5rem;">

                            @foreach ($errors->all() as $error)                            @foreach ($errors->all() as $error)

                                <li style="margin: 0.25rem 0;">{{ $error }}</li>                                <li style="margin: 0.25rem 0;">{{ $error }}</li>

                            @endforeach                            @endforeach

                        </ul>                        </ul>

                    </div>                    </div>

                </div>                </div>

            </x-filament::section>            </x-filament::section>

        @endif        @endif



        {{-- Event Stats --}}        {{-- Event Stats --}}

        <div class="stats-grid section-spacing">        <div class="stats-grid section-spacing">

            <x-filament::section>            <x-filament::section>

                <div class="stat-card">                <div class="stat-card">

                    <x-filament::icon icon="heroicon-o-square-3-stack-3d" class="stat-icon" style="color: rgb(59, 130, 246);" />                    <x-filament::icon icon="heroicon-o-square-3-stack-3d" class="stat-icon" style="color: rgb(59, 130, 246);" />

                    <div class="stat-label">Event Type</div>                    <div class="stat-label">Event Type</div>

                    <div class="stat-value">{{ ucfirst($event->judging_type) }}</div>                    <div class="stat-value">{{ ucfirst($event->judging_type) }}</div>

                </div>                </div>

            </x-filament::section>            </x-filament::section>

            <x-filament::section>            <x-filament::section>

                <div class="stat-card">                <div class="stat-card">

                    <x-filament::icon icon="heroicon-o-calculator" class="stat-icon" style="color: rgb(16, 185, 129);" />                    <x-filament::icon icon="heroicon-o-calculator" class="stat-icon" style="color: rgb(16, 185, 129);" />

                    <div class="stat-label">Scoring Mode</div>                    <div class="stat-label">Scoring Mode</div>

                    <div class="stat-value">{{ ucfirst($event->scoring_mode) }}</div>                    <div class="stat-value">{{ ucfirst($event->scoring_mode) }}</div>

                </div>                </div>

            </x-filament::section>            </x-filament::section>

            <x-filament::section>            <x-filament::section>

                <div class="stat-card">                <div class="stat-card">

                    <x-filament::icon icon="heroicon-o-user-group" class="stat-icon" style="color: rgb(245, 158, 11);" />                    <x-filament::icon icon="heroicon-o-user-group" class="stat-icon" style="color: rgb(245, 158, 11);" />

                    <div class="stat-label">Contestants</div>                    <div class="stat-label">Contestants</div>

                    <div class="stat-value">{{ $contestants->count() }}</div>                    <div class="stat-value">{{ $contestants->count() }}</div>

                </div>                </div>

            </x-filament::section>            </x-filament::section>

            @if ($event->judging_type === 'criteria')            @if ($event->judging_type === 'criteria')

                <x-filament::section>                <x-filament::section>

                    <div class="stat-card">                    <div class="stat-card">

                        <x-filament::icon icon="heroicon-o-clipboard-document-list" class="stat-icon" style="color: rgb(139, 92, 246);" />                        <x-filament::icon icon="heroicon-o-clipboard-document-list" class="stat-icon" style="color: rgb(139, 92, 246);" />

                        <div class="stat-label">Criteria</div>                        <div class="stat-label">Criteria</div>

                        <div class="stat-value">{{ $criterias->count() }}</div>                        <div class="stat-value">{{ $criterias->count() }}</div>

                    </div>                    </div>

                </x-filament::section>                </x-filament::section>

            @else            @else

                <x-filament::section>                <x-filament::section>

                    <div class="stat-card">                    <div class="stat-card">

                        <x-filament::icon icon="heroicon-o-arrow-path-rounded-square" class="stat-icon" style="color: rgb(139, 92, 246);" />                        <x-filament::icon icon="heroicon-o-arrow-path-rounded-square" class="stat-icon" style="color: rgb(139, 92, 246);" />

                        <div class="stat-label">Rounds</div>                        <div class="stat-label">Rounds</div>

                        <div class="stat-value">{{ $rounds->count() }}</div>                        <div class="stat-value">{{ $rounds->count() }}</div>

                    </div>                    </div>

                </x-filament::section>                </x-filament::section>

            @endif            @endif

        </div>        </div>



        {{-- Scoring Form --}}        {{-- Scoring Form --}}

        <x-filament::section>        <x-filament::section>

            <x-slot name="heading">            <x-slot name="heading">

                <div style="display: flex; align-items: center; gap: 0.5rem;">                <div style="display: flex; align-items: center; gap: 0.5rem;">

                    <x-filament::icon icon="heroicon-o-clipboard-document-check" style="width: 1.25rem; height: 1.25rem;" />                    <x-filament::icon icon="heroicon-o-clipboard-document-check" style="width: 1.25rem; height: 1.25rem;" />

                    <span>Enter Scores</span>                    <span>Enter Scores</span>

                </div>                </div>

            </x-slot>            </x-slot>



            <form method="POST" action="{{ route('score.store', $token) }}" x-data="scoringForm()">            <form method="POST" action="{{ route('score.store', $token) }}" x-data="scoringForm()">

                @csrf                @csrf

                                

                <div class="scoring-table">                <div class="scoring-table">

                @if ($event->judging_type === 'criteria')                @if ($event->judging_type === 'criteria')

                    {{-- Criteria-based Scoring --}}                    {{-- Criteria-based Scoring --}}

                    <table>                    <table>

                        <thead>                        <thead>

                            <tr>                            <tr>

                                <th>                                <th>

                                    <div style="display: flex; align-items: center; gap: 0.5rem;">                                    <div style="display: flex; align-items: center; gap: 0.5rem;">

                                        <x-filament::icon icon="heroicon-o-user" style="width: 1rem; height: 1rem;" />                                        <x-filament::icon icon="heroicon-o-user" style="width: 1rem; height: 1rem;" />

                                        Contestant                                        Contestant

                                    </div>                                    </div>

                                </th>                                </th>

                                @foreach ($criterias as $criteria)                                @foreach ($criterias as $criteria)

                                    <th>                                    <th>

                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">

                                            <x-filament::icon icon="heroicon-o-star" style="width: 1rem; height: 1rem; color: rgb(245, 158, 11);" />                                            <x-filament::icon icon="heroicon-o-star" style="width: 1rem; height: 1rem; color: rgb(245, 158, 11);" />

                                            <span>{{ $criteria->name }}</span>                                            <span>{{ $criteria->name }}</span>

                                        </div>                                        </div>

                                        <div class="badge-group">                                        <div class="badge-group">

                                            <x-filament::badge size="sm" color="success">Max: {{ $criteria->max_score }}</x-filament::badge>                                            <x-filament::badge size="sm" color="success">Max: {{ $criteria->max_score }}</x-filament::badge>

                                            <x-filament::badge size="sm" color="info">Weight: {{ $criteria->weight }}</x-filament::badge>                                            <x-filament::badge size="sm" color="info">Weight: {{ $criteria->weight }}</x-filament::badge>

                                        </div>                                        </div>

                                    </th>                                    </th>

                                @endforeach                                @endforeach

                            </tr>                            </tr>

                        </thead>                        </thead>

                        <tbody>                        <tbody>

                            @foreach ($contestants as $contestant)                            @foreach ($contestants as $contestant)

                                <tr>                                <tr>

                                    <td>                                    <td>

                                        <div class="contestant-cell">                                        <div class="contestant-cell">

                                            <x-filament::avatar                                             <x-filament::avatar 

                                                src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=7F9CF5&background=EBF4FF"                                                src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=7F9CF5&background=EBF4FF"

                                                size="md"                                                size="md"

                                            />                                            />

                                            <div>                                            <div>

                                                <div class="contestant-name">{{ $contestant->name }}</div>                                                <div class="contestant-name">{{ $contestant->name }}</div>

                                                @if ($contestant->description)                                                @if ($contestant->description)

                                                    <div class="contestant-desc">{{ $contestant->description }}</div>                                                    <div class="contestant-desc">{{ $contestant->description }}</div>

                                                @endif                                                @endif

                                            </div>                                            </div>

                                        </div>                                        </div>

                                    </td>                                    </td>

                                    @foreach ($criterias as $criteria)                                    @foreach ($criterias as $criteria)

                                        @php                                        @php

                                            $key = $contestant->id . '_' . $criteria->id;                                            $key = $contestant->id . '_' . $criteria->id;

                                            $existingScore = $existingScores[$key] ?? null;                                            $existingScore = $existingScores[$key] ?? null;

                                        @endphp                                        @endphp

                                        <td>                                        <td>

                                            <div class="score-input-wrapper">                                            <div class="score-input-wrapper">

                                                <x-filament::input.wrapper>                                                <x-filament::input.wrapper>

                                                    <x-filament::input                                                    <x-filament::input

                                                        type="number"                                                        type="number"

                                                        name="scores[{{ $key }}][score]"                                                        name="scores[{{ $key }}][score]"

                                                        x-model="scores['{{ $key }}'].score"                                                        x-model="scores['{{ $key }}'].score"

                                                        min="{{ $criteria->min_score ?? 0 }}"                                                        min="{{ $criteria->min_score ?? 0 }}"

                                                        max="{{ $criteria->max_score }}"                                                        max="{{ $criteria->max_score }}"

                                                        step="0.1"                                                        step="0.1"

                                                        value="{{ $existingScore?->score ?? '' }}"                                                        value="{{ $existingScore?->score ?? '' }}"

                                                    />                                                    />

                                                </x-filament::input.wrapper>                                                </x-filament::input.wrapper>

                                            </div>                                            </div>

                                            <input type="hidden" name="scores[{{ $key }}][contestant_id]" value="{{ $contestant->id }}">                                            <input type="hidden" name="scores[{{ $key }}][contestant_id]" value="{{ $contestant->id }}">

                                            <input type="hidden" name="scores[{{ $key }}][criteria_id]" value="{{ $criteria->id }}">                                            <input type="hidden" name="scores[{{ $key }}][criteria_id]" value="{{ $criteria->id }}">

                                        </td>                                        </td>

                                    @endforeach                                    @endforeach

                                </tr>                                </tr>

                            @endforeach                            @endforeach

                        </tbody>                        </tbody>

                    </table>                    </table>

                @else                @else

                    {{-- Rounds-based Scoring --}}                    {{-- Rounds-based Scoring --}}

                    @if ($event->scoring_mode === 'boolean')                    @if ($event->scoring_mode === 'boolean')

                        {{-- Boolean Mode --}}                        {{-- Boolean Mode --}}

                        <table>                        <table>

                            <thead>                            <thead>

                                <tr>                                <tr>

                                    <th>                                    <th>

                                        <div style="display: flex; align-items: center; gap: 0.5rem;">                                        <div style="display: flex; align-items: center; gap: 0.5rem;">

                                            <x-filament::icon icon="heroicon-o-user" style="width: 1rem; height: 1rem;" />                                            <x-filament::icon icon="heroicon-o-user" style="width: 1rem; height: 1rem;" />

                                            Contestant                                            Contestant

                                        </div>                                        </div>

                                    </th>                                    </th>

                                    @foreach ($rounds as $round)                                    @foreach ($rounds as $round)

                                        <th>                                        <th>

                                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">                                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">

                                                <x-filament::icon icon="heroicon-o-arrow-path" style="width: 1rem; height: 1rem; color: rgb(59, 130, 246);" />                                                <x-filament::icon icon="heroicon-o-arrow-path" style="width: 1rem; height: 1rem; color: rgb(59, 130, 246);" />

                                                <span>{{ $round->name }}</span>                                                <span>{{ $round->name }}</span>

                                            </div>                                            </div>

                                            <div class="badge-group">                                            <div class="badge-group">

                                                <x-filament::badge size="sm" color="success">{{ $round->points_per_question }} pts/Q</x-filament::badge>                                                <x-filament::badge size="sm" color="success">{{ $round->points_per_question }} pts/Q</x-filament::badge>

                                                <x-filament::badge size="sm" color="info">{{ $round->total_questions }} Qs</x-filament::badge>                                                <x-filament::badge size="sm" color="info">{{ $round->total_questions }} Qs</x-filament::badge>

                                            </div>                                            </div>

                                        </th>                                        </th>

                                    @endforeach                                    @endforeach

                                </tr>                                </tr>

                            </thead>                            </thead>

                            <tbody>                            <tbody>

                                @foreach ($contestants as $contestant)                                @foreach ($contestants as $contestant)

                                    <tr>                                    <tr>

                                        <td>                                        <td>

                                            <div class="contestant-cell">                                            <div class="contestant-cell">

                                                <x-filament::avatar                                                 <x-filament::avatar 

                                                    src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=10B981&background=D1FAE5"                                                    src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=10B981&background=D1FAE5"

                                                    size="md"                                                    size="md"

                                                />                                                />

                                                <div>                                                <div>

                                                    <div class="contestant-name">{{ $contestant->name }}</div>                                                    <div class="contestant-name">{{ $contestant->name }}</div>

                                                    @if ($contestant->description)                                                    @if ($contestant->description)

                                                        <div class="contestant-desc">{{ $contestant->description }}</div>                                                        <div class="contestant-desc">{{ $contestant->description }}</div>

                                                    @endif                                                    @endif

                                                </div>                                                </div>

                                            </div>                                            </div>

                                        </td>                                        </td>

                                        @foreach ($rounds as $round)                                        @foreach ($rounds as $round)

                                            @php                                            @php

                                                $key = $contestant->id . '_' . $round->id;                                                $key = $contestant->id . '_' . $round->id;

                                                $existingScore = $existingScores[$key] ?? null;                                                $existingScore = $existingScores[$key] ?? null;

                                            @endphp                                            @endphp

                                            <td>                                            <td>

                                                <label class="checkbox-label" :style="scores['{{ $key }}'].is_correct ? 'background-color: rgba(16, 185, 129, 0.1);' : ''">                                                <label class="checkbox-label" :style="scores['{{ $key }}'].is_correct ? 'background-color: rgba(16, 185, 129, 0.1);' : ''">

                                                    <x-filament::input.checkbox                                                    <x-filament::input.checkbox

                                                        name="scores[{{ $key }}][is_correct]"                                                        name="scores[{{ $key }}][is_correct]"

                                                        value="1"                                                        value="1"

                                                        :checked="$existingScore?->is_correct ?? false"                                                        :checked="$existingScore?->is_correct ?? false"

                                                        x-model="scores['{{ $key }}'].is_correct"                                                        x-model="scores['{{ $key }}'].is_correct"

                                                    />                                                    />

                                                    <span style="font-size: 0.875rem; font-weight: 500;" x-text="scores['{{ $key }}'].is_correct ? '✓ Correct' : '✗ Incorrect'">                                                    <span style="font-size: 0.875rem; font-weight: 500;" x-text="scores['{{ $key }}'].is_correct ? '✓ Correct' : '✗ Incorrect'">

                                                        {{ $existingScore?->is_correct ? '✓ Correct' : '✗ Incorrect' }}                                                        {{ $existingScore?->is_correct ? '✓ Correct' : '✗ Incorrect' }}

                                                    </span>                                                    </span>

                                                </label>                                                </label>

                                                <input type="hidden" name="scores[{{ $key }}][contestant_id]" value="{{ $contestant->id }}">                                                <input type="hidden" name="scores[{{ $key }}][contestant_id]" value="{{ $contestant->id }}">

                                                <input type="hidden" name="scores[{{ $key }}][round_id]" value="{{ $round->id }}">                                                <input type="hidden" name="scores[{{ $key }}][round_id]" value="{{ $round->id }}">

                                            </td>                                            </td>

                                        @endforeach                                        @endforeach

                                    </tr>                                    </tr>

                                @endforeach                                @endforeach

                            </tbody>                            </tbody>

                        </table>                        </table>

                    @else                    @else

                        {{-- Manual Mode --}}                        {{-- Manual Mode --}}

                        <table>                        <table>

                            <thead>                            <thead>

                                <tr>                                <tr>

                                    <th>                                    <th>

                                        <div style="display: flex; align-items: center; gap: 0.5rem;">                                        <div style="display: flex; align-items: center; gap: 0.5rem;">

                                            <x-filament::icon icon="heroicon-o-user" style="width: 1rem; height: 1rem;" />                                            <x-filament::icon icon="heroicon-o-user" style="width: 1rem; height: 1rem;" />

                                            Contestant                                            Contestant

                                        </div>                                        </div>

                                    </th>                                    </th>

                                    @foreach ($rounds as $round)                                    @foreach ($rounds as $round)

                                        <th>                                        <th>

                                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">                                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">

                                                <x-filament::icon icon="heroicon-o-arrow-path" style="width: 1rem; height: 1rem; color: rgb(59, 130, 246);" />                                                <x-filament::icon icon="heroicon-o-arrow-path" style="width: 1rem; height: 1rem; color: rgb(59, 130, 246);" />

                                                <span>{{ $round->name }}</span>                                                <span>{{ $round->name }}</span>

                                            </div>                                            </div>

                                            <div class="badge-group">                                            <div class="badge-group">

                                                <x-filament::badge size="sm" color="success">Max: {{ $round->max_score }}</x-filament::badge>                                                <x-filament::badge size="sm" color="success">Max: {{ $round->max_score }}</x-filament::badge>

                                            </div>                                            </div>

                                        </th>                                        </th>

                                    @endforeach                                    @endforeach

                                </tr>                                </tr>

                            </thead>                            </thead>

                            <tbody>                            <tbody>

                                @foreach ($contestants as $contestant)                                @foreach ($contestants as $contestant)

                                    <tr>                                    <tr>

                                        <td>                                        <td>

                                            <div class="contestant-cell">                                            <div class="contestant-cell">

                                                <x-filament::avatar                                                 <x-filament::avatar 

                                                    src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=6366F1&background=E0E7FF"                                                    src="https://ui-avatars.com/api/?name={{ urlencode($contestant->name) }}&color=6366F1&background=E0E7FF"

                                                    size="md"                                                    size="md"

                                                />                                                />

                                                <div>                                                <div>

                                                    <div class="contestant-name">{{ $contestant->name }}</div>                                                    <div class="contestant-name">{{ $contestant->name }}</div>

                                                    @if ($contestant->description)                                                    @if ($contestant->description)

                                                        <div class="contestant-desc">{{ $contestant->description }}</div>                                                        <div class="contestant-desc">{{ $contestant->description }}</div>

                                                    @endif                                                    @endif

                                                </div>                                                </div>

                                            </div>                                            </div>

                                        </td>                                        </td>

                                        @foreach ($rounds as $round)                                        @foreach ($rounds as $round)

                                            @php                                            @php

                                                $key = $contestant->id . '_' . $round->id;                                                $key = $contestant->id . '_' . $round->id;

                                                $existingScore = $existingScores[$key] ?? null;                                                $existingScore = $existingScores[$key] ?? null;

                                            @endphp                                            @endphp

                                            <td>                                            <td>

                                                <div class="score-input-wrapper">                                                <div class="score-input-wrapper">

                                                    <x-filament::input.wrapper>                                                    <x-filament::input.wrapper>

                                                        <x-filament::input                                                        <x-filament::input

                                                            type="number"                                                            type="number"

                                                            name="scores[{{ $key }}][score]"                                                            name="scores[{{ $key }}][score]"

                                                            x-model="scores['{{ $key }}'].score"                                                            x-model="scores['{{ $key }}'].score"

                                                            min="0"                                                            min="0"

                                                            max="{{ $round->max_score }}"                                                            max="{{ $round->max_score }}"

                                                            step="0.1"                                                            step="0.1"

                                                            value="{{ $existingScore?->score ?? '' }}"                                                            value="{{ $existingScore?->score ?? '' }}"

                                                        />                                                        />

                                                    </x-filament::input.wrapper>                                                    </x-filament::input.wrapper>

                                                </div>                                                </div>

                                                <input type="hidden" name="scores[{{ $key }}][contestant_id]" value="{{ $contestant->id }}">                                                <input type="hidden" name="scores[{{ $key }}][contestant_id]" value="{{ $contestant->id }}">

                                                <input type="hidden" name="scores[{{ $key }}][round_id]" value="{{ $round->id }}">                                                <input type="hidden" name="scores[{{ $key }}][round_id]" value="{{ $round->id }}">

                                            </td>                                            </td>

                                        @endforeach                                        @endforeach

                                    </tr>                                    </tr>

                                @endforeach                                @endforeach

                            </tbody>                            </tbody>

                        </table>                        </table>

                    @endif                    @endif

                @endif                @endif

                </div>                </div>



                <div class="form-actions">                <div class="form-actions">

                    <x-filament::button type="submit" icon="heroicon-o-check-circle">                    <x-filament::button type="submit" icon="heroicon-o-check-circle">

                        Save Scores                        Save Scores

                    </x-filament::button>                    </x-filament::button>

                </div>                </div>

            </form>            </form>

        </x-filament::section>        </x-filament::section>



        <div class="info-banner">        <div class="info-banner">

            <x-filament::badge color="info">            <x-filament::badge color="info">

                <div style="display: flex; align-items: center; gap: 0.5rem;">                <div style="display: flex; align-items: center; gap: 0.5rem;">

                    <x-filament::icon icon="heroicon-o-information-circle" style="width: 1rem; height: 1rem;" />                    <x-filament::icon icon="heroicon-o-information-circle" style="width: 1rem; height: 1rem;" />

                    <span>Your scores are saved. You can return to this page anytime using your unique link.</span>                    <span>Your scores are saved. You can return to this page anytime using your unique link.</span>

                </div>                </div>

            </x-filament::badge>            </x-filament::badge>

        </div>        </div>

    </div>    </div>



    @filamentScripts    @filamentScripts



    <script>    <script>

        function scoringForm() {        function scoringForm() {

            return {            return {

                scores: {},                scores: {},

                                

                init() {                init() {

                    this.initializeScores();                    this.initializeScores();

                },                },

                                

                initializeScores() {                initializeScores() {

                    @if ($event->judging_type === 'criteria')                    @if ($event->judging_type === 'criteria')

                        @foreach ($contestants as $contestant)                        @foreach ($contestants as $contestant)

                            @foreach ($criterias as $criteria)                            @foreach ($criterias as $criteria)

                                @php $key = $contestant->id . '_' . $criteria->id; @endphp                                @php $key = $contestant->id . '_' . $criteria->id; @endphp

                                this.scores['{{ $key }}'] = { score: {{ $existingScores[$key]->score ?? 0 }} };                                this.scores['{{ $key }}'] = { score: {{ $existingScores[$key]->score ?? 0 }} };

                            @endforeach                            @endforeach

                        @endforeach                        @endforeach

                    @elseif ($event->scoring_mode === 'boolean')                    @elseif ($event->scoring_mode === 'boolean')

                        @foreach ($contestants as $contestant)                        @foreach ($contestants as $contestant)

                            @foreach ($rounds as $round)                            @foreach ($rounds as $round)

                                @php $key = $contestant->id . '_' . $round->id; @endphp                                @php $key = $contestant->id . '_' . $round->id; @endphp

                                this.scores['{{ $key }}'] = { is_correct: {{ $existingScores[$key]->is_correct ?? 'false' }} };                                this.scores['{{ $key }}'] = { is_correct: {{ $existingScores[$key]->is_correct ?? 'false' }} };

                            @endforeach                            @endforeach

                        @endforeach                        @endforeach

                    @else                    @else

                        @foreach ($contestants as $contestant)                        @foreach ($contestants as $contestant)

                            @foreach ($rounds as $round)                            @foreach ($rounds as $round)

                                @php $key = $contestant->id . '_' . $round->id; @endphp                                @php $key = $contestant->id . '_' . $round->id; @endphp

                                this.scores['{{ $key }}'] = { score: {{ $existingScores[$key]->score ?? 0 }} };                                this.scores['{{ $key }}'] = { score: {{ $existingScores[$key]->score ?? 0 }} };

                            @endforeach                            @endforeach

                        @endforeach                        @endforeach

                    @endif                    @endif

                }                }

            }            }

        }        }

    </script>    </script>

</body></body>

</html></html>

