{{-- Div pembungkus untuk latar belakang dan pemusatan telah dipindahkan ke guest.blade.php --}}
<div>
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
