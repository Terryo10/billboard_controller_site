@props(['title' => config('app.name')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#dc2626">
    <meta name="description" content="Billboard Controller — Book advertising time slots on our network of premium digital signage screens across prime city locations.">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="Book advertising time slots on our network of premium digital signage screens. Pick a location, upload your advert, and go live.">
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">

    {{-- ─────────────────────────────────────────────────
         NAVIGATION
    ───────────────────────────────────────────────── --}}
    <nav
        x-data="{ mobileOpen: false, userOpen: false, scrolled: false }"
        @scroll.window="scrolled = window.scrollY > 10"
        :class="scrolled ? 'shadow-md' : 'shadow-sm'"
        class="bg-white/80 backdrop-blur-md border-b border-gray-200/60 sticky top-0 z-50 transition-all duration-200"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-[72px] items-center">

                {{-- Logo --}}
                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}"
                       class="flex items-center space-x-2.5 hover:scale-105 transition-transform duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 rounded-lg">
                        <div class="bg-red-600 rounded-lg p-1.5 shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                            </svg>
                        </div>
                        <span class="text-xl font-extrabold text-gray-900">Billboard<span class="text-red-600">Ctrl</span></span>
                    </a>

                    {{-- Desktop nav links --}}
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('home') }}"
                           class="relative px-3 py-2 text-sm font-medium transition-colors duration-200 rounded-md focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:outline-none
                                  {{ request()->routeIs('home') ? 'text-red-600' : 'text-gray-600 hover:text-red-600 hover:bg-red-50' }}">
                            Home
                            @if(request()->routeIs('home'))
                                <span class="absolute bottom-0 left-3 right-3 h-0.5 bg-red-600 rounded-full"></span>
                            @endif
                        </a>
                        <a href="{{ route('stations.index') }}"
                           class="relative px-3 py-2 text-sm font-medium transition-colors duration-200 rounded-md focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:outline-none
                                  {{ request()->routeIs('stations.*') ? 'text-red-600' : 'text-gray-600 hover:text-red-600 hover:bg-red-50' }}">
                            Stations
                            @if(request()->routeIs('stations.*'))
                                <span class="absolute bottom-0 left-3 right-3 h-0.5 bg-red-600 rounded-full"></span>
                            @endif
                        </a>
                        <a href="{{ route('booking.create') }}"
                           class="px-3 py-1.5 text-sm font-semibold rounded-full transition-colors duration-200 focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:outline-none
                                  {{ request()->routeIs('booking.create') ? 'bg-red-100 text-red-700' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                            Book Advert
                        </a>
                    </div>
                </div>

                {{-- Right side: auth area + mobile toggle --}}
                <div class="flex items-center space-x-3">
                    @auth
                        {{-- Authenticated user dropdown --}}
                        <div class="relative hidden md:block" x-data="{ userOpen: false }">
                            <button
                                @click="userOpen = !userOpen"
                                @keydown.escape="userOpen = false"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-xl hover:bg-gray-100 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                            >
                                <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white text-xs font-bold select-none">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <span class="text-sm font-medium text-gray-700 max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': userOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Dropdown panel --}}
                            <div
                                x-show="userOpen"
                                @click.away="userOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 origin-top-right z-50"
                                style="display: none;"
                            >
                                <div class="px-4 py-2.5 border-b border-gray-100">
                                    <p class="text-xs text-gray-400">Signed in as</p>
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                </div>
                                <a href="{{ route('my-adverts') }}"
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    My Adverts
                                </a>
                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Profile
                                </a>
                                <div class="border-t border-gray-100 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="hidden md:flex items-center space-x-2">
                            <a href="{{ route('login') }}"
                               class="text-sm font-medium text-gray-600 hover:text-red-600 transition-colors duration-200 px-3 py-2 rounded-md focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:outline-none">
                                Login
                            </a>
                            <a href="{{ route('register') }}"
                               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200 focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:outline-none">
                                Get Started
                            </a>
                        </div>
                    @endauth

                    {{-- Mobile hamburger --}}
                    <button
                        @click="mobileOpen = !mobileOpen"
                        class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:outline-none"
                        aria-label="Toggle menu"
                    >
                        <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: block;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        {{-- Mobile menu drawer --}}
        <div
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden bg-white border-t border-gray-100 shadow-lg"
            style="display: none;"
        >
            <div class="py-3 px-4 space-y-1">
                <a href="{{ route('home') }}"
                   @click="mobileOpen = false"
                   class="flex items-center py-3 px-3 text-base font-medium rounded-xl transition-colors {{ request()->routeIs('home') ? 'text-red-600 bg-red-50' : 'text-gray-700 hover:text-red-600 hover:bg-red-50' }}">
                    Home
                </a>
                <a href="{{ route('stations.index') }}"
                   @click="mobileOpen = false"
                   class="flex items-center py-3 px-3 text-base font-medium rounded-xl transition-colors {{ request()->routeIs('stations.*') ? 'text-red-600 bg-red-50' : 'text-gray-700 hover:text-red-600 hover:bg-red-50' }}">
                    Stations
                </a>
                <a href="{{ route('booking.create') }}"
                   @click="mobileOpen = false"
                   class="flex items-center py-3 px-3 text-base font-medium rounded-xl transition-colors text-gray-700 hover:text-red-600 hover:bg-red-50">
                    Book Advert
                </a>
            </div>
            <div class="border-t border-gray-100 py-3 px-4 space-y-1">
                @auth
                    <div class="flex items-center gap-3 px-3 py-2 mb-2">
                        <div class="w-9 h-9 rounded-full bg-red-600 flex items-center justify-center text-white text-sm font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('my-adverts') }}"
                       @click="mobileOpen = false"
                       class="flex items-center py-3 px-3 text-base font-medium rounded-xl text-gray-700 hover:text-red-600 hover:bg-red-50 transition-colors">
                        My Adverts
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       @click="mobileOpen = false"
                       class="flex items-center py-3 px-3 text-base font-medium rounded-xl text-gray-700 hover:text-red-600 hover:bg-red-50 transition-colors">
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-center py-3 px-3 text-base font-medium rounded-xl text-red-600 hover:bg-red-50 transition-colors">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       @click="mobileOpen = false"
                       class="flex items-center py-3 px-3 text-base font-medium rounded-xl text-gray-700 hover:text-red-600 hover:bg-red-50 transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       @click="mobileOpen = false"
                       class="flex items-center justify-center py-3 px-3 text-base font-semibold rounded-xl text-white bg-red-600 hover:bg-red-700 transition-colors">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ─────────────────────────────────────────────────
         TOAST FLASH MESSAGES
    ───────────────────────────────────────────────── --}}
    @if(session('success'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-[-12px]"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-[-12px]"
            class="fixed top-4 right-4 z-[100] max-w-sm w-full pointer-events-auto"
            style="display: none;"
        >
            <div class="bg-white border border-green-200 border-l-4 border-l-green-500 shadow-xl rounded-xl p-4 flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">Success</p>
                    <p class="text-sm text-gray-600 mt-0.5">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-[-12px]"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-[-12px]"
            class="fixed top-4 right-4 z-[100] max-w-sm w-full pointer-events-auto"
            style="display: none;"
        >
            <div class="bg-white border border-red-200 border-l-4 border-l-red-500 shadow-xl rounded-xl p-4 flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">Error</p>
                    <p class="text-sm text-gray-600 mt-0.5">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- ─────────────────────────────────────────────────
         MAIN CONTENT
    ───────────────────────────────────────────────── --}}
    <main>
        {{ $slot }}
    </main>

    {{-- ─────────────────────────────────────────────────
         FOOTER
    ───────────────────────────────────────────────── --}}
    <div class="h-1 bg-red-600"></div>
    <footer class="bg-slate-900 text-slate-400 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

                {{-- Column 1: Brand --}}
                <div class="lg:col-span-1">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2.5 mb-4">
                        <div class="bg-red-600 rounded-lg p-1.5">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                            </svg>
                        </div>
                        <span class="text-lg font-extrabold text-white">Billboard<span class="text-red-400">Ctrl</span></span>
                    </a>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Premium digital signage advertising across prime city locations. Reach your audience where it matters.
                    </p>
                    {{-- Social icons --}}
                    <div class="flex items-center space-x-3 mt-5">
                        <a href="#" aria-label="Twitter" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-red-600 flex items-center justify-center text-slate-400 hover:text-white transition-all duration-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="LinkedIn" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-red-600 flex items-center justify-center text-slate-400 hover:text-white transition-all duration-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="Instagram" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-red-600 flex items-center justify-center text-slate-400 hover:text-white transition-all duration-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Column 2: Product --}}
                <div>
                    <h3 class="text-white font-semibold text-sm mb-4 uppercase tracking-wide">Product</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('stations.index') }}" class="text-slate-400 hover:text-white transition-colors duration-200">View Stations</a></li>
                        <li><a href="{{ route('booking.create') }}" class="text-slate-400 hover:text-white transition-colors duration-200">Book Advert</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors duration-200">Pricing</a></li>
                        @auth
                            <li><a href="{{ route('my-adverts') }}" class="text-slate-400 hover:text-white transition-colors duration-200">My Adverts</a></li>
                        @endauth
                    </ul>
                </div>

                {{-- Column 3: Resources --}}
                <div>
                    <h3 class="text-white font-semibold text-sm mb-4 uppercase tracking-wide">Resources</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('home') }}#how-it-works" class="text-slate-400 hover:text-white transition-colors duration-200">How It Works</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors duration-200">FAQs</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors duration-200">Contact Us</a></li>
                        <li><a href="/admin" class="text-slate-400 hover:text-white transition-colors duration-200">Admin Panel</a></li>
                    </ul>
                </div>

                {{-- Column 4: Newsletter / Contact --}}
                <div>
                    <h3 class="text-white font-semibold text-sm mb-4 uppercase tracking-wide">Stay Updated</h3>
                    <p class="text-sm text-slate-400 mb-4">Get notified about new locations and platform updates.</p>
                    <form @submit.prevent class="flex flex-col gap-2">
                        <input
                            type="email"
                            placeholder="your@email.com"
                            class="bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors duration-200">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="border-t border-slate-800 mt-10 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Billboard Controller. All rights reserved.</p>
                <div class="flex items-center space-x-4 text-xs text-slate-500">
                    <a href="#" class="hover:text-slate-300 transition-colors">Privacy Policy</a>
                    <span class="text-slate-700">|</span>
                    <a href="#" class="hover:text-slate-300 transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
