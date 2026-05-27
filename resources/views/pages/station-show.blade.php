<x-public-layout>
    <x-slot:title>{{ $station->name }} — Billboard Controller</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('stations.index') }}" class="hover:text-blue-600">Stations</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ $station->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Station Info --}}
            <div class="lg:col-span-2 space-y-6">
                @if($station->photo)
                    <img src="{{ $station->photo_url }}" alt="{{ $station->name }}" class="w-full h-72 object-cover rounded-2xl shadow">
                @else
                    <div class="w-full h-72 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-24 h-24 text-white opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                        </svg>
                    </div>
                @endif

                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $station->name }}</h1>
                    <p class="text-gray-500 mt-1 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        {{ $station->location_name }}
                    </p>
                    @if($station->description)
                        <p class="text-gray-600 mt-4 leading-relaxed">{{ $station->description }}</p>
                    @endif
                </div>

                {{-- Specs --}}
                @if($station->screen_size || $station->screen_width)
                <div class="bg-gray-50 rounded-xl p-5 grid grid-cols-3 gap-4">
                    @if($station->screen_size)
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $station->screen_size }}</p>
                        <p class="text-xs text-gray-500">Screen Size</p>
                    </div>
                    @endif
                    @if($station->screen_width && $station->screen_height)
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $station->screen_width }}×{{ $station->screen_height }}</p>
                        <p class="text-xs text-gray-500">Resolution (px)</p>
                    </div>
                    @endif
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $station->isOnline() ? 'Online' : 'Offline' }}</p>
                        <p class="text-xs text-gray-500">Device Status</p>
                    </div>
                </div>
                @endif

                {{-- Map --}}
                @if($station->lat && $station->lng)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Location</h2>
                    <div id="station-map" class="w-full h-56 rounded-xl shadow"></div>
                </div>
                @endif
            </div>

            {{-- Pricing & Booking CTA --}}
            <div class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Pricing</h2>
                    @if($station->timeSlotTemplates->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($station->timeSlotTemplates->groupBy('day_of_week') as $day => $slots)
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                        {{ ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][$day] }}
                                    </p>
                                    @foreach($slots as $slot)
                                        <div class="flex justify-between items-center py-1 text-sm">
                                            <span class="text-gray-700">{{ substr($slot->start_time, 0, 5) }} – {{ substr($slot->end_time, 0, 5) }}</span>
                                            <span class="font-semibold text-blue-600">${{ number_format($slot->price, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg text-xs text-blue-700">
                            💡 Bulk discount: 10% for 5+ slots, 20% for 10+ slots
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No pricing configured yet.</p>
                    @endif
                    <a href="{{ route('booking.create', ['station' => $station->id]) }}" class="mt-6 block text-center bg-blue-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-blue-700 transition">
                        Book This Station
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($station->lat && $station->lng)
    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endpush
    @push('scripts')
    <script>
        const map = L.map('station-map').setView([{{ $station->lat }}, {{ $station->lng }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        L.marker([{{ $station->lat }}, {{ $station->lng }}]).addTo(map)
            .bindPopup('<strong>{{ addslashes($station->name) }}</strong>').openPopup();
    </script>
    @endpush
    @endif
</x-public-layout>
