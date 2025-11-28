<x-guest-layout>
        <div class="relative min-h-screen">

        <!-- Imagen de fondo -->
        <div class="absolute inset-0 bg-center bg-cover bg-no-repeat z-0"
             style="background-image: url('{{ asset('images/fondo-muni.jpg') }}'); opacity: 0.45;">
        </div>

        <!-- Contenido del login -->
        <div class="relative z-10 flex items-center justify-center min-h-screen px-4">
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('¿Olvidaste tu contraseña? No hay problema. Solo ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña y elegir una nueva.') }}
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="Correo electrónico" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    Enviar enlace de restablecimiento
                </x-button>
            </div>
        </form>
    </x-authentication-card>

        </div>
    

</x-guest-layout>