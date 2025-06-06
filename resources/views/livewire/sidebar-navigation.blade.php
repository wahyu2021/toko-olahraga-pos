<div>
    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 h-screen bg-blue-700 text-white 
               transition-all duration-300 ease-in-out overflow-y-auto overflow-x-hidden 
               {{ $sidebarOpen ? 'w-64' : 'w-20' }}">

        <div class="flex flex-col h-full">
            {{-- Tombol toggle sidebar & Logo --}}
            <div class="{{ $sidebarOpen ? 'px-4 pt-4' : 'px-4 pt-4 flex flex-col items-center' }}"> {{-- Added pt-4 for top padding --}}
                <div class="w-full flex {{ $sidebarOpen ? 'justify-between' : 'justify-center' }} items-center mb-6">
                    {{-- Logo --}}
                    <a href="{{ route('dashboard') }}" {{-- Dashboard utama akan redirect berdasarkan peran --}}
                        class="{{ !$sidebarOpen ? 'mx-auto' : '' }} flex flex-col justify-center items-center text-center">
                        @if ($sidebarOpen)
                            <img src="{{ asset('images/icon-web.png') }}" alt="Logo Aplikasi" class="h-16 w-auto mb-2">
                            {{-- Atur ukuran logo --}}
                            <span class="text-lg font-bold text-wrap leading-tight">Toko Olahraga</span>
                        @else
                            <img src="{{ asset('images/icon-web.png') }}" alt="Logo Ringkas" class="h-10 w-auto">
                            {{-- Logo lebih kecil saat sidebar tertutup --}}
                        @endif
                    </a>
                </div>

                {{-- Navigasi utama --}}
                <nav class="space-y-1 w-full">

                    {{-- Cek Peran Pengguna untuk Menampilkan Navigasi yang Sesuai --}}
                    @if (Auth::check())
                        @if (Auth::user()->isAdminPusat())
                            {{-- === NAVIGASI UNTUK ADMIN PUSAT === --}}
                            <a href="{{ route('admin-pusat.dashboard') }}"
                                title="{{ !$sidebarOpen ? __('Dashboard Admin Pusat') : '' }}"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-blue-600 group transition-colors duration-150
                                {{ request()->routeIs('admin-pusat.dashboard') ? 'bg-blue-800' : '' }}
                                {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-home class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span
                                    class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">{{ __('Dashboard') }}</span>
                            </a>

                            <a href="{{ route('admin-pusat.management.users') }}"
                                title="{{ !$sidebarOpen ? __('Admin Pusat Management Users') : '' }}"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-blue-600 group transition-colors duration-150
                                {{ request()->routeIs('admin-pusat.management.users') ? 'bg-blue-800' : '' }}
                                {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-user class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span
                                    class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">{{ __('Management Users') }}</span>
                            </a>

                            <a href="{{ route('admin-pusat.management.products') }}"
                                title="{{ !$sidebarOpen ? __('Manajemen Produk') : '' }}"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-blue-600 group transition-colors duration-150
                                {{ request()->routeIs('admin-pusat.management.products') ? 'bg-blue-800' : '' }}
                                {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-cube class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                {{-- Ganti ikon jika perlu --}}
                                <span
                                    class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">{{ __('Management Products') }}</span>
                            </a>

                            <a href="{{ route('admin-pusat.management.stocks') }}"
                                title="{{ !$sidebarOpen ? __('Manajemen Produk') : '' }}"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-blue-600 group transition-colors duration-150
                                {{ request()->routeIs('admin-pusat.management.stocks') ? 'bg-blue-800' : '' }}
                                {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-archive-box class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                {{-- Ganti ikon jika perlu --}}
                                <span
                                    class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">{{ __('Management Stocks') }}</span>
                            </a>

                            <a href="{{ route('admin-pusat.reports.financial') }}"
                                title="{{ !$sidebarOpen ? __('Manajemen Produk') : '' }}"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-blue-600 group transition-colors duration-150
                                {{ request()->routeIs('admin-pusat.reports.financial') ? 'bg-blue-800' : '' }}
                                {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-chart-pie class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                {{-- Ganti ikon jika perlu --}}
                                <span
                                    class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">{{ __('Reports Financial') }}</span>
                            </a>

                            <a href="{{ route('admin-pusat.forecasting.demand') }}"
                                title="{{ !$sidebarOpen ? __('Manajemen Produk') : '' }}"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-blue-600 group transition-colors duration-150
                                {{ request()->routeIs('admin-pusat.forecasting.demand') ? 'bg-blue-800' : '' }}
                                {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-presentation-chart-line
                                    class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                {{-- Ganti ikon jika perlu --}}
                                <span
                                    class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">{{ __('Demand Forecasting') }}</span>
                            </a>
                        @endif
                    @endif
                    {{-- Akhir dari link navigasi baru --}}
                </nav>
            </div>

            {{-- User Info & Logout --}}
            <div
                class="mt-auto border-t border-blue-600 {{ $sidebarOpen ? 'p-4' : 'p-2 pt-4 flex flex-col items-center' }}">
                @if (Auth::check())
                    @if ($sidebarOpen)
                        <div class="flex items-center mb-3">
                            <img class="h-10 w-10 rounded-full object-cover"
                                src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-blue-200">{{ Str::title(Auth::user()->email) }}</p>
                            </div>
                        </div>
                    @endif
                @endif
                <div class="mt-1 space-y-1 w-full"> {{-- reduce mt if sidebar not open and no user info --}}
                    <a href="{{ route('profile.show') }}" title="{{ !$sidebarOpen ? __('Profile') : '' }}"
                        class="flex items-center py-2.5 px-3 rounded-md hover:bg-blue-600 group transition-colors duration-150 w-full
                               {{ request()->routeIs('profile.show') ? 'bg-blue-800' : '' }}
                               {{ !$sidebarOpen ? 'justify-center' : '' }}">
                        <x-heroicon-o-user-circle class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                        <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">{{ __('Profile') }}</span>
                    </a>

                    @if (Auth::check() &&
                            Laravel\Jetstream\Jetstream::hasApiFeatures() &&
                            Auth::user()->isAdminPusat() &&
                            Auth::user()->isAdminCabang())
                        {{-- API Tokens hanya untuk Admin --}}
                        <a href="{{ route('api-tokens.index') }}" title="{{ !$sidebarOpen ? __('API Tokens') : '' }}"
                            class="flex items-center py-2.5 px-3 rounded-md hover:bg-blue-600 group transition-colors duration-150 w-full
                                   {{ request()->routeIs('api-tokens.index') ? 'bg-blue-800' : '' }}
                                   {{ !$sidebarOpen ? 'justify-center' : '' }}">
                            <x-heroicon-o-key class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                            <span
                                class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">{{ __('API Tokens') }}</span>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" x-data class="w-full">
                        @csrf
                        <a href="{{ route('logout') }}" @click.prevent="$root.submit();"
                            title="{{ !$sidebarOpen ? __('Log Out') : '' }}"
                            class="flex items-center py-2.5 px-3 rounded-md hover:bg-blue-600 group w-full transition-colors duration-150
                                   {{ !$sidebarOpen ? 'justify-center' : '' }}">
                            <x-heroicon-o-arrow-left-on-rectangle
                                class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                            <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">{{ __('Log Out') }}</span>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </aside>
    {{-- Konten Utama tidak ada di sini --}}
</div>
