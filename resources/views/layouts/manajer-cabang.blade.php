<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js']) 

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-200" x-data="{ sidebarCurrentlyOpen: true }"
    x-on:sidebar-state-changed.window="sidebarCurrentlyOpen = $event.detail.open">

    <div class="min-h-screen" id="main-content">
        {{-- Komponen Livewire untuk Sidebar --}}
        @livewire('sidebar-navigation')

        {{-- Wrapper untuk Konten Utama yang akan bergeser --}}
        <div id="main-content-wrapper" class="transition-all duration-300 ease-in-out "
            {{-- pt untuk ruang dari hamburger --}}
            :class="{
                'md:ml-64': sidebarCurrentlyOpen,
                'md:ml-20': !sidebarCurrentlyOpen
                {{-- Di layar kecil (<md), defaultnya ml-0, sidebar akan overlay --}}
            }">

            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>
    </div>



    @livewireScripts
    @stack('modals')
    {{-- <script>
        function checkDesktopView() {
            const minDesktopWidth = 1024;
            const mainContent = document.getElementById('main-content');
            const messageDiv = document.getElementById('desktop-only-message');

            if (window.innerWidth < minDesktopWidth) {
                if (mainContent) mainContent.style.display = 'none';
                if (messageDiv) messageDiv.style.display = 'flex';
            } else {
                if (mainContent) mainContent.style.display = 'block'; // Atau 'flex', dll. tergantung layout Anda
                if (messageDiv) messageDiv.style.display = 'none';
            }
        }

        // Jalankan saat halaman dimuat
        window.onload = checkDesktopView;
    </script> --}}
</body>

</html>
