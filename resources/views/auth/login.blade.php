<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <x-jet-authentication-card class="w-full max-w-md p-6 bg-white shadow-md rounded-lg">
            <x-slot name="logo">
                <img src="{{ url('dist/img/PNG/VirtualEqubLogoIcon.png') }}" alt="Logo" class="mx-auto mb-4" style="width:100px">
            </x-slot>

            <x-jet-validation-errors class="mb-4" />

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <x-jet-label for="email" value="{{ __('Email') }}" class="font-semibold" />
                    <x-jet-input id="email" class="block mt-1 w-full border border-gray-300 rounded-md focus:ring focus:ring-blue-300" type="email" name="email" :value="old('email')" required autofocus />
                </div>

                <div class="mt-4">
                    <x-jet-label for="password" value="{{ __('Password') }}" class="font-semibold" />
                    <x-jet-input id="password" class="block mt-1 w-full border border-gray-300 rounded-md focus:ring focus:ring-blue-300" type="password" name="password" required autocomplete="current-password" />
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="flex items-center">
                        <x-jet-checkbox id="remember_me" name="remember" class="rounded" />
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <button type="submit" class="advanced-login-button">
                        Login
                    </button>
                </div>
            </form>
        </x-jet-authentication-card>
    </div>
</x-guest-layout>

<style>
    .advanced-login-button {
        background: linear-gradient(135deg, #4e73df, #1cc88a); /* Gradient background */
        color: white; /* Text color */
        border: none; /* Remove default border */
        border-radius: 25px; /* Rounded corners */
        padding: 12px 24px; /* Padding for size */
        font-size: 16px; /* Font size */
        font-weight: bold; /* Bold text */
        text-transform: uppercase; /* Uppercase text */
        cursor: pointer; /* Pointer cursor on hover */
        transition: background 0.3s ease, transform 0.2s; /* Smooth transition */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Shadow effect */
        margin-left: 10px; /* Space between the link and button */
    }

    .advanced-login-button:hover {
        background: linear-gradient(135deg, #1cc88a, #4e73df); /* Reverse gradient on hover */
        transform: translateY(-2px); /* Slight upward movement */
    }

    .advanced-login-button:active {
        transform: translateY(1px); /* Downward movement on click */
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2); /* Reduced shadow on click */
    }

    /* Additional styles for better visuals */
    .flex {
        display: flex;
        align-items: center;
    }
    
    .justify-between {
        justify-content: space-between;
    }

    .bg-gray-100 {
        background-color: #f7fafc; /* Light gray background */
    }

    .rounded-lg {
        border-radius: 0.5rem; /* Rounded corners for the card */
    }
</style>