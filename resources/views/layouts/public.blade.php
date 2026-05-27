<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Billboard Controller') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                        </svg>
                        <span class="text-xl font-bold text-gray-900">Billboard</span>
                    </a>
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium transition">Home</a>
                        <a href="{{ route('stations.index') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium transition">Stations</a>
                        <a href="{{ route('booking.create') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium transition">Book Advert</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('my-adverts') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium transition">My Adverts</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-red-600 transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-blue-600 transition font-medium">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 m-4 max-w-7xl mx-auto rounded">
            <p class="text-green-700 text-sm">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 m-4 max-w-7xl mx-auto rounded">
            <p class="text-red-700 text-sm">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white font-semibold mb-3">Billboard Controller</h3>
                    <p class="text-sm">The platform for digital signage advertising. Reach your audience on screens across prime locations.</p>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-3">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('stations.index') }}" class="hover:text-white transition">View Stations</a></li>
                        <li><a href="{{ route('booking.create') }}" class="hover:text-white transition">Book Now</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-3">Admin</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/admin" class="hover:text-white transition">Admin Panel</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-sm text-center">
                &copy; {{ date('Y') }} Billboard Controller. All rights reserved.
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
