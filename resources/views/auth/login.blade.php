<x-guest-layout>
    {{-- Karena guest-layout sudah mengatur pemusatan, kita hanya perlu mendefinisikan "kartu" nya di sini --}}
    <div
        class="relative flex flex-col m-6 space-y-8 bg-white shadow-2xl rounded-2xl md:flex-row md:space-y-0 w-full max-w-4xl">

        {{-- Panel Kiri (Branding) --}}
        <div class="relative hidden md:flex md:w-1/2">
            <div
                class="absolute inset-0 bg-gradient-to-br from-orange-500 to-orange-700 rounded-l-2xl flex flex-col justify-center items-center text-white p-8">
                <div class="">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Aplikasi" class="h-56 w-auto">
                </div>
                <h1 class="text-4xl font-bold mb-3">Toko Olahraga Ujang</h1>
                <p class="text-center text-orange-100">Sistem POS dan Manajemen Stok Terintegrasi.</p>
            </div>
        </div>

        {{-- Panel Kanan (Form Login) --}}
        <div class="flex flex-col justify-center p-8 md:p-14 md:w-1/2">

            <div class="md:hidden flex justify-center mb-4">
                <x-authentication-card-logo />
            </div>

            <h2 class="mb-3 text-4xl font-bold">Selamat Datang!</h2>
            <p class="mb-8 text-gray-600">
                Silakan masuk ke akun Anda untuk melanjutkan.
            </p>

            <x-validation-errors class="mb-4" />

            @session('status')
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ $value }}
                </div>
            @endsession

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                {{-- Input Email dengan Ikon --}}
                <div>
                    <x-label for="email" value="{{ __('Email') }}" class="mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-at-symbol class="h-5 w-5 text-gray-400" />
                        </div>
                        <x-input id="email" class="block w-full ps-10" type="email" name="email"
                            :value="old('email')" required autofocus autocomplete="username" />
                    </div>
                </div>

                {{-- Input Password dengan Ikon --}}
                <div>
                    <x-label for="password" value="{{ __('Password') }}" class="mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-lock-closed class="h-5 w-5 text-gray-400" />
                        </div>
                        <x-input id="password" class="block w-full ps-10" type="password" name="password" required
                            autocomplete="current-password" />
                    </div>
                </div>

                {{-- Opsi Remember Me & Lupa Password --}}
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="flex items-center">
                        <x-checkbox id="remember_me" name="remember" />
                        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-orange-600 hover:text-orange-800 hover:underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                            href="{{ route('password.request') }}">
                            {{ __('Lupa password?') }}
                        </a>
                    @endif
                </div>

                {{-- Tombol Login --}}
                <div>
                    <x-button
                        class="w-full flex justify-center bg-orange-600 hover:bg-orange-700 active:bg-orange-800 focus:ring-orange-500">
                        {{ __('Log In') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
