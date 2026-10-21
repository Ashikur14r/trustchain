<x-guest-layout>
    <h1 class="text-lg font-medium text-gray-900">Verify your sign in</h1>
    <p class="mt-2 text-sm text-gray-600">Enter the six-digit code from your authenticator app.</p>
    <form method="POST" action="{{ route('login.2fa.verify') }}" class="mt-6">
        @csrf
        <x-input-label for="code" value="Authenticator code" />
        <x-text-input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required autofocus class="block mt-1 w-full" />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
        <x-primary-button class="mt-4">Verify</x-primary-button>
    </form>
</x-guest-layout>
