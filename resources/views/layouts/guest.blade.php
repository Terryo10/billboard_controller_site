<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#dc2626">
    <title>{{ config('app.name', 'Billboard Controller') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">

        {{-- ── Left brand panel (hidden on mobile) ── --}}
        <div class="hidden lg:flex lg:w-1/2 bg-slate-900 flex-col justify-between p-12 relative overflow-hidden">
            {{-- Subtle dot texture --}}
            <div class="absolute inset-0 opacity-[0.04]"
                 style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 20px 20px;"></div>

            {{-- Top: Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 relative z-10">
                <div class="bg-red-600 rounded-xl p-2 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                    </svg>
                </div>
                <span class="text-xl font-extrabold text-white">Billboard<span class="text-red-400">Ctrl</span></span>
            </a>

            {{-- Middle: Headline + feature points --}}
            <div class="relative z-10">
                <h2 class="text-4xl font-extrabold text-white leading-tight mb-4">
                    Advertise on<br>
                    <span class="text-red-400">Digital Screens</span><br>
                    Across the City
                </h2>
                <p class="text-slate-400 text-base leading-relaxed mb-10 max-w-sm">
                    Book slots on our network of premium digital billboards. Upload your creative and go live in minutes.
                </p>

                <ul class="space-y-4">
                    @foreach([
                        ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Real-time slot availability'],
                        ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',   'text' => 'Instant booking confirmation'],
                        ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'text' => 'Secure payments, cancel anytime'],
                    ] as $point)
                        <li class="flex items-center gap-3 text-sm text-slate-300">
                            <div class="w-7 h-7 rounded-full bg-red-600/20 border border-red-600/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $point['icon'] }}"/>
                                </svg>
                            </div>
                            {{ $point['text'] }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Bottom: Stat strip --}}
            <div class="flex items-center gap-8 relative z-10">
                <div>
                    <p class="text-2xl font-extrabold text-white">500+</p>
                    <p class="text-xs text-slate-500 mt-0.5">Advertisers</p>
                </div>
                <div class="h-8 w-px bg-slate-700"></div>
                <div>
                    <p class="text-2xl font-extrabold text-white">24/7</p>
                    <p class="text-xs text-slate-500 mt-0.5">Live screens</p>
                </div>
                <div class="h-8 w-px bg-slate-700"></div>
                <div>
                    <p class="text-2xl font-extrabold text-white">10+</p>
                    <p class="text-xs text-slate-500 mt-0.5">Cities</p>
                </div>
            </div>
        </div>

        {{-- ── Right form panel ── --}}
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 bg-gray-50">
            {{-- Mobile-only logo --}}
            <div class="lg:hidden mb-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <div class="bg-red-600 rounded-lg p-1.5">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold text-gray-900">Billboard<span class="text-red-600">Ctrl</span></span>
                </a>
            </div>

            <div class="w-full max-w-md">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-8 py-10">
                    {{ $slot }}
                </div>

                <p class="text-center text-xs text-gray-400 mt-6">
                    <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">&larr; Back to home</a>
                </p>
            </div>
        </div>

    </div>
</body>
</html>
