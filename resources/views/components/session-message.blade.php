@if (session()->has('success'))
    <div class="mb-4 rounded-md bg-green-100 border-l-4 border-green-500 p-4" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <x-heroicon-o-check-circle class="h-5 w-5 text-green-500" />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    </div>
@endif

@if (session()->has('error'))
    <div class="mb-4 rounded-md bg-red-100 border-l-4 border-red-500 p-4" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <x-heroicon-o-x-circle class="h-5 w-5 text-red-500" />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                    {{ session('error') }}
                </p>
            </div>
        </div>
    </div>
@endif