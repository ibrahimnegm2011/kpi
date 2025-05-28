<x-guest-layout>
    <p class="text-gray-600 text-lg font-bold mb-6 text-center">Sign in to your account</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @error('email')
    <x-auth-session-status class="mb-4 p-2 bg-red-100 text-red-500 italic font-bold" :status="$message" />
    @enderror

    <form method="POST" action="{{ route('login') }}" class="w-full space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
            <input id="email" type="email" name="email" required autofocus value="{{ old('email') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" type="password" name="password" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
        </div>
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-700">Remember me</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-green-700 hover:underline">Forgot password?</a>
        </div>
        <x-primary-button>
            {{ __('Sign in') }}
        </x-primary-button>
    </form>
</x-guest-layout>
