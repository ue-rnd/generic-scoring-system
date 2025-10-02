<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - Judge Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Events</h1>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Welcome, {{ Auth::user()->name }}</span>
                <a href="/admin" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Admin Panel</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Logout</button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if ($events->count() > 0)
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($events as $event)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-semibold text-gray-900">{{ $event->name }}</h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $event->judging_type === 'criteria' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($event->judging_type) }}-based
                            </span>
                        </div>
                        
                        @if ($event->description)
                            <p class="text-gray-600 mb-4">{{ Str::limit($event->description, 100) }}</p>
                        @endif
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Start Date:</span>
                                <span class="font-medium">{{ $event->start_date->format('M j, Y g:i A') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">End Date:</span>
                                <span class="font-medium">{{ $event->end_date->format('M j, Y g:i A') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Contestants:</span>
                                <span class="font-medium">{{ $event->contestants->count() }}</span>
                            </div>
                            @if ($event->judging_type === 'criteria')
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Criteria:</span>
                                    <span class="font-medium">{{ $event->criterias->count() }}</span>
                                </div>
                            @else
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Rounds:</span>
                                    <span class="font-medium">{{ $event->rounds->count() }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('judge.event.show', $event) }}" 
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded hover:bg-blue-700 transition">
                                Score Event
                            </a>
                            <a href="{{ route('judge.results', $event) }}" 
                               class="flex-1 bg-green-600 text-white text-center py-2 px-4 rounded hover:bg-green-700 transition">
                                View Results
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Events Assigned</h3>
                <p class="text-gray-500">You haven't been assigned to any events yet.</p>
            </div>
        @endif
    </div>
</body>
</html>


