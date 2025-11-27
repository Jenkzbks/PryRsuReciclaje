<x-guest-layout>
     <div class="relative min-h-screen">

        <!-- Imagen de fondo -->
        <div class="absolute inset-0 bg-center bg-cover bg-no-repeat z-0"
             style="background-image: url('{{ asset('images/fondo-muni.jpg') }}'); opacity: 0.45;">
        </div>
                <div class="relative z-10 flex items-center justify-center min-h-screen px-4">

    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="Nombre" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="Correo electrónico" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="Contraseña" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="Confirmar contraseña" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2 text-sm text-gray-600">
                                Acepto los <a target="_blank" href="{{ route('terms.show') }}" class="underline hover:text-gray-900">Términos del servicio</a> y la <a target="_blank" href="{{ route('policy.show') }}" class="underline hover:text-gray-900">Política de privacidad</a>.
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    ¿Ya estás registrado?
                </a>

                <x-button class="ms-4">
                    Registrarse
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
        </div>
    </div>