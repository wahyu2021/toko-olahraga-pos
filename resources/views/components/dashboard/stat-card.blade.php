@props(['title', 'value', 'icon', 'color' => 'gray'])

@php
    // Menentukan kelas warna berdasarkan prop 'color'
    $borderColor = "border-{$color}-500";
    $bgColor = "bg-{$color}-500";
@endphp

<div class="bg-white shadow-sm rounded-lg p-5 border-l-4 {{ $borderColor }}">
    <div class="flex items-center">
        <div class="flex-shrink-0 {{ $bgColor }} rounded-md p-3">
            <x-dynamic-component :component="'heroicon-o-'.$icon" class="h-6 w-6 text-white" />
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 truncate">{{ $title }}</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $value }}</p>
        </div>
    </div>
</div>