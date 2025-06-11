<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    <style>
        /* Menambahkan style khusus untuk video background */
        #background-video {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -100;
            object-fit: cover;
            /* Memastikan video menutupi seluruh area */
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased">

    {{-- Elemen Video untuk Background --}}
    <video autoplay muted loop id="background-video">
        {{-- Menggunakan helper asset() untuk menunjuk ke video di folder public --}}
        <source src="{{ asset('videos/bg-video.mp4') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    {{-- Overlay Gelap untuk Keterbacaan --}}
    <div class="fixed inset-0 bg-black bg-opacity-50"></div>

    {{-- Konten Utama (Form Login, Register, dll.) --}}
    <div class="relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        {{ $slot }}
    </div>

    @livewireScripts
</body>

</html>
