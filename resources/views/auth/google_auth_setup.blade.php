<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Set up two-factor authentication</h2></x-slot>
    <div class="py-12"><div class="max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow sm:rounded-lg">
            <p class="text-sm text-gray-600">Add this secret to your authenticator app, then enter the current six-digit code to enable two-factor authentication.</p>
            <p class="mt-4 font-mono break-all">{{ $secret }}</p>
            <p class="mt-2 text-xs text-gray-500">Account: {{ $user->email }} · Issuer: TrustChain</p>
            <form method="POST" action="{{ route('two-factor.confirm') }}" class="mt-6">
                @csrf
                <x-input-label for="code" value="Authenticator code" />
                <x-text-input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required class="block mt-1 w-full" />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                <x-primary-button class="mt-4">Enable two-factor authentication</x-primary-button>
            </form>
        </div>
    </div></div>
</x-app-layout>
