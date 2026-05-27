<x-public-layout>
    <x-slot:title>My Adverts — Billboard Controller</x-slot:title>

    @php
        $pageBookings    = $bookings->getCollection();
        $totalCount      = $bookings->total();
        $activeCount     = $pageBookings->where('status', 'approved')->count();
        $pendingCount    = $pageBookings->where('status', 'pending')->count();
        $cancelledCount  = $pageBookings->where('status', 'cancelled')->count();
        $totalSpent      = $pageBookings->whereNotNull('paid_at')->sum('total_price');
    @endphp

    {{-- ═══════════════════════════════════════════════
         WELCOME HEADER
    ═══════════════════════════════════════════════ --}}
    <div class="bg-red-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    {{-- Breadcrumb --}}
                    <nav class="flex items-center gap-1.5 text-red-300 text-xs mb-3">
                        <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-white font-medium">My Adverts</span>
                    </nav>
                    <h1 class="text-2xl font-bold text-white">
                        Welcome back, {{ Auth::user()->name ?? 'Advertiser' }}
                    </h1>
                    <p class="text-red-200 mt-1 text-sm">Here's an overview of your advertising campaigns</p>
                    <p class="text-red-300/70 text-xs mt-0.5">{{ now()->format('l, F j, Y') }}</p>
                </div>
                <a href="{{ route('booking.create') }}"
                   class="inline-flex items-center gap-2 bg-white text-red-700 font-bold px-5 py-2.5 rounded-xl hover:bg-red-50 shadow-md transition-all duration-200 self-start sm:self-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Booking
                </a>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS  (overlap the gradient header)
    ═══════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-5 relative z-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            {{-- Total Bookings --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-900">{{ $totalCount }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Total Bookings</p>
                </div>
            </div>

            {{-- Active Adverts --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-900">{{ $activeCount }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Active Adverts</p>
                </div>
            </div>

            {{-- Pending Review --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-900">{{ $pendingCount }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Pending Review</p>
                </div>
            </div>

            {{-- Total Spent --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-900">${{ number_format($totalSpent, 0) }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Total Spent</p>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         FILTER TABS + BOOKING LIST
    ═══════════════════════════════════════════════ --}}
    @if($bookings->isEmpty())

        {{-- ─── GLOBAL EMPTY STATE ─── --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="text-center py-24 bg-white rounded-2xl border border-dashed border-gray-200">
                <svg class="w-28 h-28 text-gray-200 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                </svg>
                <h2 class="text-xl font-bold text-gray-900 mt-6">No bookings yet</h2>
                <p class="text-gray-500 text-sm mt-2 max-w-xs mx-auto">You haven't made any bookings yet. Choose a billboard station and launch your first campaign.</p>
                <a href="{{ route('booking.create') }}"
                   class="inline-block mt-6 bg-red-600 text-white px-8 py-3 rounded-xl font-semibold hover:bg-red-700 transition-colors duration-200">
                    Book Your First Advert &rarr;
                </a>
                <div class="mt-3">
                    <a href="{{ route('stations.index') }}" class="text-red-600 text-sm hover:underline">
                        Browse available stations
                    </a>
                </div>
            </div>
        </div>

    @else

        <div x-data="{
            activeTab: 'all',
            allCount: {{ $totalCount }},
            activeCount: {{ $activeCount }},
            pendingCount: {{ $pendingCount }},
            cancelledCount: {{ $cancelledCount }},
        }">

            {{-- ─── FILTER TABS ─── --}}
            <div class="bg-white border-b border-gray-200 sticky top-[72px] z-30 mt-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex space-x-0 overflow-x-auto">

                        <button @click="activeTab = 'all'"
                                :class="activeTab === 'all' ? 'border-b-2 border-red-600 text-red-600 font-semibold' : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700'"
                                class="py-4 px-4 text-sm font-medium cursor-pointer transition-colors duration-150 whitespace-nowrap flex items-center gap-2">
                            All
                            <span :class="activeTab === 'all' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600'"
                                  class="text-xs px-2 py-0.5 rounded-full font-semibold transition-colors">
                                {{ $totalCount }}
                            </span>
                        </button>

                        <button @click="activeTab = 'approved'"
                                :class="activeTab === 'approved' ? 'border-b-2 border-red-600 text-red-600 font-semibold' : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700'"
                                class="py-4 px-4 text-sm font-medium cursor-pointer transition-colors duration-150 whitespace-nowrap flex items-center gap-2">
                            Active
                            <span :class="activeTab === 'approved' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                  class="text-xs px-2 py-0.5 rounded-full font-semibold transition-colors">
                                {{ $activeCount }}
                            </span>
                        </button>

                        <button @click="activeTab = 'pending'"
                                :class="activeTab === 'pending' ? 'border-b-2 border-red-600 text-red-600 font-semibold' : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700'"
                                class="py-4 px-4 text-sm font-medium cursor-pointer transition-colors duration-150 whitespace-nowrap flex items-center gap-2">
                            Pending
                            <span :class="activeTab === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600'"
                                  class="text-xs px-2 py-0.5 rounded-full font-semibold transition-colors">
                                {{ $pendingCount }}
                            </span>
                        </button>

                        <button @click="activeTab = 'cancelled'"
                                :class="activeTab === 'cancelled' ? 'border-b-2 border-red-600 text-red-600 font-semibold' : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700'"
                                class="py-4 px-4 text-sm font-medium cursor-pointer transition-colors duration-150 whitespace-nowrap flex items-center gap-2">
                            Cancelled
                            <span :class="activeTab === 'cancelled' ? 'bg-gray-200 text-gray-700' : 'bg-gray-100 text-gray-600'"
                                  class="text-xs px-2 py-0.5 rounded-full font-semibold transition-colors">
                                {{ $cancelledCount }}
                            </span>
                        </button>

                    </div>
                </div>
            </div>

            {{-- ─── BOOKING LIST ─── --}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

                {{-- Record count --}}
                <p class="text-sm text-gray-400 mb-5">
                    Showing {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} of {{ $totalCount }} booking{{ $totalCount !== 1 ? 's' : '' }}
                </p>

                <div class="space-y-4">
                    @foreach($bookings as $index => $booking)

                        @php
                            $statusConfig = match($booking->status) {
                                'approved'  => ['bar' => 'bg-green-500',  'badge' => 'bg-green-100 text-green-700',   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>'],
                                'pending'   => ['bar' => 'bg-amber-400',  'badge' => 'bg-amber-100 text-amber-700',   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                                'rejected'  => ['bar' => 'bg-red-500',    'badge' => 'bg-red-100 text-red-700',       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>'],
                                'cancelled' => ['bar' => 'bg-gray-300',   'badge' => 'bg-gray-100 text-gray-600',     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>'],
                                default     => ['bar' => 'bg-gray-300',   'badge' => 'bg-gray-100 text-gray-600',     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.197 2.622-2.962 2.9C12.417 13.28 12 13.617 12 14v1m0 4h.01"/>'],
                            };

                            $advertStatusConfig = match($booking->advert?->status ?? '') {
                                'approved'       => ['badge' => 'bg-green-100 text-green-700',  'label' => 'Approved'],
                                'pending_review' => ['badge' => 'bg-amber-100 text-amber-700',  'label' => 'Pending Review'],
                                'rejected'       => ['badge' => 'bg-red-100 text-red-700',      'label' => 'Rejected'],
                                default          => ['badge' => 'bg-gray-100 text-gray-600',    'label' => ucfirst($booking->advert?->status ?? 'N/A')],
                            };
                        @endphp

                        <div
                            x-show="activeTab === 'all' || activeTab === '{{ $booking->status }}'"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden flex animate-fade-in-up animation-delay-{{ min($index * 100 + 100, 500) }}"
                        >
                            {{-- Left status accent bar --}}
                            <div class="w-1.5 flex-shrink-0 {{ $statusConfig['bar'] }}"></div>

                            {{-- Card body --}}
                            <div class="flex-1 p-6">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                                    {{-- Left content --}}
                                    <div class="flex-1 min-w-0">
                                        {{-- Station + status --}}
                                        <div class="flex flex-wrap items-center gap-3 mb-1">
                                            <h3 class="font-bold text-gray-900 text-lg">{{ $booking->station->name }}</h3>
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold {{ $statusConfig['badge'] }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    {!! $statusConfig['icon'] !!}
                                                </svg>
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </div>

                                        {{-- Location --}}
                                        <p class="text-gray-500 text-sm flex items-center gap-1.5 mb-3">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ $booking->station->location_name }}
                                        </p>

                                        {{-- Details row --}}
                                        <div class="flex flex-wrap gap-x-6 gap-y-2">
                                            {{-- Slot count --}}
                                            <span class="flex items-center gap-1.5 text-sm text-gray-600">
                                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="1.75"/>
                                                    <line x1="3" y1="10" x2="21" y2="10" stroke-width="1.75"/>
                                                    <line x1="8" y1="2" x2="8" y2="6" stroke-width="1.75"/>
                                                    <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.75"/>
                                                </svg>
                                                {{ $booking->slot_count }} slot{{ $booking->slot_count !== 1 ? 's' : '' }}
                                            </span>

                                            {{-- Price --}}
                                            <span class="flex items-center gap-1.5 text-sm text-gray-600 font-semibold">
                                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                ${{ number_format($booking->total_price, 2) }}
                                            </span>

                                            {{-- Booked date --}}
                                            <span class="flex items-center gap-1.5 text-sm text-gray-500">
                                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Booked {{ $booking->created_at->format('M j, Y') }}
                                            </span>

                                            {{-- Paid badge --}}
                                            @if($booking->paid_at)
                                                <span class="flex items-center gap-1.5 text-sm text-emerald-600 font-semibold">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Paid {{ $booking->paid_at->format('M j, Y') }}
                                                </span>
                                            @endif

                                            {{-- First air date --}}
                                            @if($booking->bookingSlots->isNotEmpty())
                                                @php $firstSlot = $booking->bookingSlots->sortBy('air_date')->first(); @endphp
                                                <span class="flex items-center gap-1.5 text-sm text-gray-500">
                                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Airs {{ $firstSlot->air_date->format('M j, Y') }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Advert status --}}
                                        @if($booking->advert)
                                            <div class="mt-4 pt-4 border-t border-gray-50 flex flex-col gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-medium text-gray-500">Advert:</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $advertStatusConfig['badge'] }}">
                                                        {{ $advertStatusConfig['label'] }}
                                                    </span>
                                                </div>
                                                @if($booking->advert->rejection_reason)
                                                    <div class="flex items-start gap-2 bg-red-50 border border-red-100 rounded-lg p-3">
                                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                        <p class="text-sm text-red-600">{{ $booking->advert->rejection_reason }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Action buttons --}}
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if(in_array($booking->status, ['pending']) && !$booking->paid_at)
                                            <a href="{{ route('booking.payment', $booking) }}"
                                               class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg font-semibold shadow-sm transition-colors duration-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                </svg>
                                                Pay Now
                                            </a>
                                        @endif
                                        @if(in_array($booking->status, ['pending', 'approved']) && $booking->bookingSlots->where('air_date', '>', today())->isNotEmpty())
                                            <form method="POST" action="{{ route('booking.cancel', $booking) }}"
                                                  onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 border border-red-200 text-red-600 hover:bg-red-50 text-sm px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>

                    @endforeach
                </div>

                {{-- Tab-specific empty message --}}
                <div
                    x-show="activeTab !== 'all' && document.querySelectorAll('[x-show]:not([style*=\'display: none\'])').length === 0"
                    class="text-center py-12 text-gray-400 text-sm"
                    style="display: none;"
                >
                    No bookings in this category on this page.
                </div>

                {{-- Pagination --}}
                @if($bookings->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $bookings->links() }}
                    </div>
                @endif

            </div>
        </div>

    @endif

</x-public-layout>
