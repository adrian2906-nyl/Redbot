<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-indigo-500 via-purple-500 to-violet-600 text-white">
        <div class="w-full max-w-md bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl p-8">
            <h2 class="text-3xl font-bold text-center mb-6 text-white">Iniciar Sesión</h2>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-center text-green-200" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white">Correo electrónico</label>
                    <input id="email" name="email" type="email"
                        class="mt-1 block w-full rounded-lg border-0 p-3 text-gray-900 shadow-sm focus:ring-2 focus:ring-purple-400"
                        value="{{ old('email') }}" required autofocus />
                    @error('email')
                        <p class="text-sm text-red-300 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white">Contraseña</label>
                    <input id="password" name="password" type="password"
                        class="mt-1 block w-full rounded-lg border-0 p-3 text-gray-900 shadow-sm focus:ring-2 focus:ring-purple-400"
                        required />
                    @error('password')
                        <p class="text-sm text-red-300 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember" class="flex items-center space-x-2 text-sm text-gray-200">
                        <input id="remember" type="checkbox" name="remember" class="rounded border-gray-300">
                        <span>Recordarme</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-200 hover:text-white transition" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-0.5">
                        Iniciar Sesión
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-300 mt-6">
                ¿No tienes una cuenta?
                <a href="{{ route('register') }}" class="text-white font-semibold hover:underline">Regístrate</a>
            </p>
        </div>
    </div>
</x-guest-layout>
