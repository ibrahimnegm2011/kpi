<x-guest-layout>
    <p class="text-gray-600 text-lg font-bold mb-3 text-center">Complete your Registration</p>
    <p class="text-gray-600 text-lg font-bold mb-6 text-center">{{$user->agent_accounts[0]->name}}</p>

    <form method="POST" action="{{ route('account.onboarding.store', $user) }}" class="w-full space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
            <input id="email" type="email" name="email" value="{{$user->email}}" disabled
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:border-gray-200 disabled:bg-gray-50 disabled:text-gray-500 disabled:shadow-none">
        </div>
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input id="name" type="text" name="name" value="{{$user->name}}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm disable:bg-gray-300 focus:border-green-500 focus:ring-green-500">
            @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" type="password" name="password" autofocus required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('password') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('password_confirmation') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <x-primary-button>
            {{ __('Register') }}
        </x-primary-button>
    </form>
</x-guest-layout>
