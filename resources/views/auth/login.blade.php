<x-guest-layout>
    <div class="relative min-h-screen">

        <!-- Imagen de fondo -->
        <div class="absolute inset-0 bg-center bg-cover bg-no-repeat z-0"
             style="background-image: url('{{ asset('images/fondo-muni.jpg') }}'); opacity: 0.45;">
        </div>

        <!-- Contenido del login -->
        <div class="relative z-10 flex items-center justify-center min-h-screen px-4">
            <x-authentication-card>
                

                <x-validation-errors class="mb-4" />

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <x-slot name="logo">
                    <x-authentication-card-logo />
                </x-slot>
                    <div>
                        <x-label for="email" value="Correo electrónico" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    </div>

                    <div class="mt-4">
                        <x-label for="password" value="Contraseña" />
                        <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    </div>

                    <div class="block mt-4">
                        <label for="remember_me" class="flex items-center">
                            <x-checkbox id="remember_me" name="remember" />
                            <span class="ms-2 text-sm text-gray-600">Recuérdame</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif

                        <x-button class="ms-4">
                            Iniciar sesión
                        </x-button>
                    </div>
                </form>
            </x-authentication-card>
        </div>

    </div>
</x-guest-layout>
