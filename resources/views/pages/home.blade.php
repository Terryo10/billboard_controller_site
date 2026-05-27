<x-public-layout>
    <x-slot:title>Billboard Controller — Advertise on Digital Screens Across the City</x-slot:title>

    {{-- ═══════════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════════ --}}
    <section class="relative bg-slate-900 text-white overflow-hidden">

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 md:py-36">
            <div class="lg:grid lg:grid-cols-2 lg:gap-16 lg:items-center">

                {{-- Left: Text content --}}
                <div class="animate-fade-in-up">
                    <div class="inline-flex items-center gap-2 bg-red-500/20 border border-red-500/30 text-red-300 text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
                        <span class="w-1.5 h-1.5 bg-red-400 rounded-full animate-pulse-slow"></span>
                        {{ $stations->count() }} screens live across the city
                    </div>

                    <h1 class="text-5xl md:text-6xl font-extrabold leading-tight mb-6">
                        Get Your Brand on<br>
                        <span class="text-red-400">
                            Digital Screens
                        </span>
                    </h1>

                    <p class="text-lg text-red-200/80 max-w-xl leading-relaxed mb-10">
                        Book advertising time slots on our network of premium digital signage screens.
                        Pick a location, upload your advert, and go live — it's that simple.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('booking.create') }}"
                           class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-8 py-4 rounded-xl text-base shadow-xl transition-all duration-200 hover:-translate-y-0.5">
                            Book Advert Now
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        <a href="{{ route('stations.index') }}"
                           class="inline-flex items-center justify-center gap-2 border-2 border-white/30 text-white hover:bg-white/10 font-semibold px-8 py-4 rounded-xl text-base transition-all duration-200">
                            View Locations
                        </a>
                    </div>

                    {{-- Trust badges --}}
                    <div class="flex flex-wrap gap-x-5 gap-y-2 mt-8 text-sm text-red-200/70">
                        @foreach(['No setup fees', 'Instant availability', 'Cancel anytime'] as $badge)
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $badge }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Billboard mockup --}}
                <div class="hidden lg:flex items-center justify-center mt-12 lg:mt-0 animate-fade-in-up animation-delay-200">
                    <div class="relative">
                        {{-- Billboard frame --}}
                        <div class="relative animate-float bg-gray-900 rounded-2xl shadow-2xl shadow-black/60 p-3 border border-white/10 w-[380px]">
                            {{-- Screen display --}}
                            <div class="bg-red-700 rounded-xl aspect-video flex flex-col items-center justify-center p-6 relative overflow-hidden">
                                <svg class="w-12 h-12 text-white/40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                                </svg>
                                <p class="text-white font-extrabold text-xl tracking-widest">YOUR AD HERE</p>
                                <p class="text-red-200/70 text-xs mt-1">Reach thousands daily</p>
                            </div>
                            {{-- Mockup base stand --}}
                            <div class="flex justify-center mt-3">
                                <div class="w-16 h-2 bg-gray-700 rounded-full"></div>
                            </div>
                            <div class="flex justify-center">
                                <div class="w-8 h-4 bg-gray-800 rounded-b-lg"></div>
                            </div>
                        </div>

                        {{-- Floating status chip --}}
                        <div class="absolute -top-4 -right-4 bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5 animate-pulse-slow">
                            <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                            LIVE
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll indicator --}}
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1 animate-pulse-slow">
            <p class="text-xs text-red-300/60 tracking-widest uppercase">Scroll</p>
            <svg class="w-5 h-5 text-red-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════
         STATS BAR
    ═══════════════════════════════════════════════ --}}
    <section class="bg-white border-y border-gray-100 py-10">
        <div class="max-w-5xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                @foreach([
                    ['value' => $stations->count(), 'label' => 'Active Screens'],
                    ['value' => '10+',               'label' => 'Cities Covered'],
                    ['value' => '500+',              'label' => 'Advertisers Served'],
                    ['value' => '24/7',              'label' => 'Live Broadcasting'],
                ] as $index => $stat)
                    <div class="text-center {{ $index < 3 ? 'md:border-r md:border-gray-100' : '' }}">
                        <p class="text-4xl font-extrabold text-red-600">{{ $stat['value'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════
         HOW IT WORKS
    ═══════════════════════════════════════════════ --}}
    <section id="how-it-works" class="bg-gray-50 py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold text-gray-900">How It Works</h2>
                <p class="text-gray-500 mt-3 text-lg">Three simple steps to get your advert live on screen</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                {{-- Connector line --}}
                <div class="hidden md:block absolute top-14 left-[calc(33.33%+2rem)] right-[calc(33.33%+2rem)] h-0.5 border-t-2 border-dashed border-red-200 z-0"></div>

                @foreach([
                    [
                        'step'  => '1',
                        'title' => 'Pick a Station',
                        'desc'  => 'Browse our network of digital screens and choose the location that reaches your target audience.',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        'delay' => 'animation-delay-100',
                    ],
                    [
                        'step'  => '2',
                        'title' => 'Choose Time Slots',
                        'desc'  => 'Select the days and times you want your advert to play. See real-time availability.',
                        'icon'  => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="1.75"/><line x1="16" y1="2" x2="16" y2="6" stroke-width="1.75"/><line x1="8" y1="2" x2="8" y2="6" stroke-width="1.75"/><line x1="3" y1="10" x2="21" y2="10" stroke-width="1.75"/>',
                        'delay' => 'animation-delay-200',
                    ],
                    [
                        'step'  => '3',
                        'title' => 'Upload & Go Live',
                        'desc'  => 'Upload your image or video, complete payment, and your advert goes live after a quick review.',
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>',
                        'delay' => 'animation-delay-300',
                    ],
                ] as $item)
                <div class="relative bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 animate-fade-in-up {{ $item['delay'] }} z-10">
                    {{-- Step badge --}}
                    <div class="absolute -top-4 left-8 w-9 h-9 rounded-full bg-red-600 text-white text-sm font-bold flex items-center justify-center shadow-lg shadow-red-200">
                        {{ $item['step'] }}
                    </div>
                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center mb-5 mt-2">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $item['icon'] !!}
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $item['title'] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $item['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════
         AVAILABLE LOCATIONS
    ═══════════════════════════════════════════════ --}}
    <section class="bg-white py-24" x-data="{ search: '' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Section header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-10">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-extrabold text-gray-900">Available Locations</h2>
                        <span class="bg-red-100 text-red-700 text-sm font-semibold px-3 py-1 rounded-full">
                            {{ $stations->count() }} screens
                        </span>
                    </div>
                    <p class="text-gray-500 mt-1.5 text-base">Find the perfect spot for your campaign</p>
                </div>
                {{-- Search --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Search stations..."
                        class="pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent w-full sm:w-64 bg-gray-50"
                    >
                </div>
            </div>

            {{-- Map --}}
            <div class="rounded-2xl overflow-hidden shadow-xl border border-gray-200 mb-14">
                <div id="map" class="w-full h-[420px] bg-gray-100"></div>
            </div>

            {{-- Station Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($stations as $station)
                    <div
                        x-show="search === '' || '{{ strtolower(addslashes($station->name)) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($station->location_name ?? '')) }}'.includes(search.toLowerCase())"
                        class="bg-white rounded-2xl border border-gray-100 hover:border-red-200 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group"
                    >
                        {{-- Image --}}
                        <div class="overflow-hidden">
                            @if($station->photo)
                                <img src="{{ $station->photo_url }}"
                                     alt="{{ $station->name }}"
                                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-48 bg-red-500 flex items-center justify-center group-hover:scale-105 transition-transform duration-500">
                                    <svg class="w-14 h-14 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Card body --}}
                        <div class="p-6">
                            <h3 class="font-bold text-gray-900 text-lg">{{ $station->name }}</h3>
                            <p class="text-gray-500 text-sm mt-1.5 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $station->location_name }}
                            </p>
                            @if($station->screen_size)
                                <p class="text-xs text-gray-400 mt-0.5 ml-5">{{ $station->screen_size }} screen</p>
                            @endif

                            <div class="mt-4 flex items-center justify-between">
                                @if($station->timeSlotTemplates->isNotEmpty())
                                    <span class="bg-red-50 text-red-700 font-semibold text-sm px-3 py-1 rounded-full">
                                        From ${{ number_format($station->timeSlotTemplates->min('price'), 2) }}/slot
                                    </span>
                                @else
                                    <span></span>
                                @endif
                                <a href="{{ route('stations.show', $station) }}"
                                   class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                                    View &amp; Book
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-16">
                        <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">No stations available yet.</p>
                        <p class="text-gray-400 text-sm mt-1">Check back soon — new locations are added regularly.</p>
                    </div>
                @endforelse
            </div>

            {{-- No search results message --}}
            @if($stations->isNotEmpty())
                <p
                    x-show="search !== '' && document.querySelectorAll('[x-show*=\"search\"]:not([style*=\"display: none\"])').length === 0"
                    class="text-center text-gray-400 text-sm mt-8"
                    style="display: none;"
                >
                    No stations match your search.
                </p>
            @endif
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════
         FINAL CTA SECTION
    ═══════════════════════════════════════════════ --}}
    <section class="bg-red-700 py-24 px-4 text-white">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-4xl font-extrabold leading-tight">
                Ready to Reach Thousands<br>of Potential Customers?
            </h2>
            <p class="text-red-200 text-lg mt-4 max-w-xl mx-auto">
                Join hundreds of businesses that use Billboard Controller to get their message in front of the right people at the right time.
            </p>

            <a href="{{ route('register') }}"
               class="inline-block mt-10 bg-white text-red-700 font-bold px-10 py-4 rounded-xl text-lg hover:bg-red-50 shadow-xl shadow-black/20 hover:-translate-y-0.5 transition-all duration-200">
                Start Advertising Today
            </a>

            @guest
                <p class="mt-4 text-red-200/70 text-sm">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-white underline hover:text-red-200 transition-colors">Log in here</a>
                </p>
            @endguest
        </div>
    </section>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            font-family: 'Figtree', sans-serif;
        }
        .leaflet-popup-tip-container { display: none; }
    </style>
    @endpush

    @push('scripts')
    <script>
        const stations = @json($mapStations);

        if (typeof L !== 'undefined' && stations.length > 0) {
            const map = L.map('map', { zoomControl: true }).setView([stations[0].lat, stations[0].lng], 13);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://carto.com/">CARTO</a> &copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            const markerIcon = L.divIcon({
                className: '',
                html: '<div style="width:28px;height:28px;background:#dc2626;border-radius:50% 50% 50% 0;transform:rotate(-45deg);box-shadow:0 2px 8px rgba(220,38,38,0.4);border:2px solid white;"></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 28],
                popupAnchor: [0, -30],
            });

            stations.forEach(s => {
                L.marker([s.lat, s.lng], { icon: markerIcon })
                    .addTo(map)
                    .bindPopup(
                        `<div style="padding:4px 2px;min-width:160px;">
                            <strong style="font-size:14px;color:#111827;">${s.name}</strong>
                            <p style="color:#6b7280;font-size:12px;margin:4px 0 8px;">${s.location}</p>
                            <a href="${s.url}" style="background:#dc2626;color:white;font-size:12px;font-weight:600;padding:6px 14px;border-radius:8px;text-decoration:none;display:inline-block;">
                                View &amp; Book
                            </a>
                        </div>`
                    );
            });
        }
    </script>
    @endpush

</x-public-layout>
