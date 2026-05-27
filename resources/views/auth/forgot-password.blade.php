<x-guest-layout>
    <div class="mb-7">
        <h1 class="text-2xl font-extrabold text-gray-900">Reset your password</h1>
        <p class="text-sm text-gray-500 mt-2">Enter your email and we'll send you a reset link.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center mt-6">
            {{ __('Send reset link') }}
        </x-primary-button>

        <p class="text-center text-sm text-gray-500 mt-5">
            <a href="{{ route('login') }}" class="text-red-600 hover:text-red-700 font-semibold transition-colors">&larr; Back to sign in</a>
        </p>
    </form>
</x-guest-layout>
