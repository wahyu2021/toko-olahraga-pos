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
            /* Latar belakang light gray untuk kontras */
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        {{-- Header Sederhana untuk POS dengan tema oranye --}}
        <header class="bg-orange-500 text-white shadow-md">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="#">
                            <h1 class="text-xl font-semibold">{{ config('app.name', 'Laravel') }} - POS</h1>
                        </a>
                    </div>
                    <div class="flex items-center">
                        @auth
                            <span class="mr-3"><span class="hidden sm:inline">Kasir:</span>
                                {{ Auth::user()->name }}</span>
                            <span class="hidden sm:inline mr-3">|</span>
                            <span class="mr-3"><span class="hidden sm:inline">Cabang:</span>
                                {{ Auth::user()->branch->name ?? 'N/A' }}</span>
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <a href="{{ route('logout') }}" @click.prevent="$root.submit();"
                                    class="hover:text-orange-100 transition-colors duration-150 ease-in-out bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700 focus:bg-red-800">
                                    {{ __('Log Out') }}
                                </a>
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
