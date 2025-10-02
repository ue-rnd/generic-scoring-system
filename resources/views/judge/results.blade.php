<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results: {{ $event->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->name }} - Results</h1>
                <p class="text-gray-600 mt-2">{{ $event->description }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('judge.events') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Back to Events</a>
                <a href="{{ route('judge.event.show', $event) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Score Event</a>
            </div>
        </div>

        <!-- Judge Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Judge Progress</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($judgeSummary as $summary)
                    <div class="bg-gray-50 p-4 rounded">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $summary['judge']->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $summary['scores_count'] }} scores submitted</p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-600">{{ number_format($summary['completion_percentage'], 1) }}%</div>
                                <div class="text-xs text-gray-500">Complete</div>
                            </div>
                        </div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $summary['completion_percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold">Final Rankings</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contestant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Final Score</th>
                            @if ($event->judging_type === 'criteria')
                                @foreach ($event->criterias as $criteria)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $criteria->name }}
                                        <div class="text-xs text-gray-400">Weight: {{ $criteria->weight }}</div>
                                    </th>
                                @endforeach
                            @else
                                @foreach ($event->rounds as $round)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $round->name }}
                                    </th>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($results as $result)
                            <tr class="{{ $result['rank'] <= 3 ? 'bg-yellow-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($result['rank'] == 1)
                                            <span class="text-2xl">🥇</span>
                                        @elseif ($result['rank'] == 2)
                                            <span class="text-2xl">🥈</span>
                                        @elseif ($result['rank'] == 3)
                                            <span class="text-2xl">🥉</span>
                                        @else
                                            <span class="text-lg font-bold text-gray-600">#{{ $result['rank'] }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $result['contestant']->name }}</div>
                                    @if ($result['contestant']->description)
                                        <div class="text-sm text-gray-500">{{ $result['contestant']->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-blue-600">{{ number_format($result['final_score'], 2) }}</div>
                                </td>
                                
                                @if ($event->judging_type === 'criteria')
                                    @foreach ($event->criterias as $criteria)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @php
                                                $criteriaScore = 0;
                                                $scores = \App\Models\Score::where('contestant_id', $result['contestant']->id)
                                                    ->where('criteria_id', $criteria->id)
                                                    ->pluck('score');
                                                if ($scores->count() > 0) {
                                                    $criteriaScore = $scores->average();
                                                }
                                            @endphp
                                            {{ number_format($criteriaScore, 2) }}
                                        </td>
                                    @endforeach
                                @else
                                    @foreach ($event->rounds as $round)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @php
                                                $roundScore = 0;
                                                $scores = \App\Models\Score::where('contestant_id', $result['contestant']->id)
                                                    ->where('round_id', $round->id)
                                                    ->pluck('score');
                                                if ($scores->count() > 0) {
                                                    $roundScore = $scores->sum();
                                                }
                                            @endphp
                                            {{ number_format($roundScore, 2) }}
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detailed Breakdown -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Detailed Scoring Breakdown</h2>
            
            @foreach ($results as $result)
                <div class="mb-6 border border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">
                        {{ $result['rank'] }}. {{ $result['contestant']->name }}
                        <span class="text-sm font-normal text-gray-500">(Final Score: {{ number_format($result['final_score'], 2) }})</span>
                    </h3>
                    
                    @if ($event->judging_type === 'criteria')
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($event->criterias as $criteria)
                                <div class="bg-gray-50 p-3 rounded">
                                    <div class="font-medium text-gray-900">{{ $criteria->name }}</div>
                                    <div class="text-sm text-gray-600">Weight: {{ $criteria->weight }}</div>
                                    @php
                                        $scores = \App\Models\Score::where('contestant_id', $result['contestant']->id)
                                            ->where('criteria_id', $criteria->id)
                                            ->with('judge')
                                            ->get();
                                        $averageScore = $scores->count() > 0 ? $scores->average('score') : 0;
                                    @endphp
                                    <div class="text-lg font-bold text-blue-600">{{ number_format($averageScore, 2) }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $scores->count() }} judge(s) scored
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($event->rounds as $round)
                                <div class="bg-gray-50 p-3 rounded">
                                    <div class="font-medium text-gray-900">{{ $round->name }}</div>
                                    <div class="text-sm text-gray-600">Max: {{ $round->max_score }}</div>
                                    @php
                                        $scores = \App\Models\Score::where('contestant_id', $result['contestant']->id)
                                            ->where('round_id', $round->id)
                                            ->with('judge')
                                            ->get();
                                        $totalScore = $scores->sum('score');
                                    @endphp
                                    <div class="text-lg font-bold text-blue-600">{{ number_format($totalScore, 2) }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $scores->count() }} score(s) submitted
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>


