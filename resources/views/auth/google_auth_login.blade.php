<x-guest-layout>
    <h1 class="text-lg font-medium text-gray-900">Authenticator sign in</h1>
    <form method="POST" action="{{ route('two-factor.login.verify') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="block mt-1 w-full" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="one_time_password" value="Six-digit code" />
            <x-text-input id="one_time_password" name="one_time_password" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required class="block mt-1 w-full" />
            <x-input-error :messages="$errors->get('one_time_password')" class="mt-2" />
        </div>
        <x-primary-button>Sign in</x-primary-button>
    </form>
</x-guest-layout>
