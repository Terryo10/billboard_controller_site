<x-public-layout>
    <x-slot:title>All Stations — Billboard Controller</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">All Advertising Stations</h1>
            <p class="text-gray-500 mt-2">{{ $stations->total() }} stations available across the network</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($stations as $station)
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden border border-gray-100">
                @if($station->photo)
                    <img src="{{ $station->photo_url }}" alt="{{ $station->name }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                        </svg>
                    </div>
                @endif
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">{{ $station->name }}</h3>
                            <p class="text-gray-500 text-sm mt-0.5">{{ $station->location_name }}</p>
                        </div>
                        <span class="flex-shrink-0 w-2.5 h-2.5 rounded-full mt-1.5 {{ $station->isOnline() ? 'bg-green-400' : 'bg-gray-300' }}" title="{{ $station->isOnline() ? 'Online' : 'Offline' }}"></span>
                    </div>
                    @if($station->screen_size)
                        <p class="text-gray-400 text-xs mt-1">{{ $station->screen_size }} screen</p>
                    @endif
                    <div class="mt-4 flex items-center justify-between">
                        @if($station->timeSlotTemplates->isNotEmpty())
                            <span class="text-blue-600 font-semibold text-sm">From ${{ number_format($station->timeSlotTemplates->min('price'), 2) }}/slot</span>
                        @else
                            <span></span>
                        @endif
                        <a href="{{ route('stations.show', $station) }}" class="bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                            View & Book
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $stations->links() }}
        </div>
    </div>
</x-public-layout>
