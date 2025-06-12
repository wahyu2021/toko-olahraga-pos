@props(['title', 'icon', 'color' => 'gray'])

@php
    $borderColor = "border-{$color}-500";
    $iconColor = "text-{$color}-700";
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 {{ $borderColor }}">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <x-dynamic-component :component="'heroicon-o-'.$icon" class="h-6 w-6 mr-2 {{ $iconColor }}" />
            {{ $title }}
        </h3>

        <ul class="divide-y divide-gray-200">
            {{ $slot }}
        </ul>
    </div>
</div>