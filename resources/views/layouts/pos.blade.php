<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - POS</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    <style>
        body {
            background-color: #f8fafc;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        {{-- Navbar Baru dengan Tampilan yang Ditingkatkan --}}
        <header class="bg-orange-600 text-white shadow-lg z-20">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">

                    {{-- Sisi Kiri: Logo dan Nama Aplikasi --}}
                    <div class="flex items-center space-x-4">
                        <a href="#" class="flex items-center space-x-3">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto">
                            <span class="text-xl font-semibold hidden sm:inline">{{ config('app.name', 'Laravel') }} -
                                POS</span>
                        </a>
                    </div>

                    {{-- Sisi Kanan: Info Pengguna dan Tombol Logout --}}
                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="hidden md:flex items-center space-x-4">
                                {{-- Info Kasir --}}
                                <div class="flex items-center space-x-2">
                                    <x-heroicon-o-user-circle class="h-5 w-5 text-orange-200" />
                                    <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                                </div>
                                {{-- Info Cabang --}}
                                <div class="flex items-center space-x-2">
                                    <x-heroicon-o-building-storefront class="h-5 w-5 text-orange-200" />
                                    <span class="text-sm font-medium">{{ Auth::user()->branch->name ?? 'N/A' }}</span>
                                </div>
                            </div>

                            {{-- Form Logout dengan Tombol Berikon --}}
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <button type="submit" @click.prevent="$root.submit();"
                                    class="flex items-center space-x-2 px-4 py-2 bg-white text-orange-600 font-semibold rounded-lg shadow-sm hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-orange-600 focus:ring-white transition-colors duration-150 ease-in-out">
                                    <x-heroicon-o-arrow-left-on-rectangle class="h-5 w-5" />
                                    <span class="hidden sm:inline">{{ __('Log Out') }}</span>
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>
