<div>
    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 h-screen bg-orange-700 text-white 
               transition-all duration-300 ease-in-out overflow-y-auto overflow-x-hidden 
               {{ $sidebarOpen ? 'w-64' : 'w-20' }}">

        <div class="flex flex-col h-full">
            {{-- Logo --}}
            <div class="{{ $sidebarOpen ? 'px-4 pt-4' : 'px-4 pt-4 flex flex-col items-center' }}">
                <div class="w-full flex justify-center items-center mb-6">
                    <a href="{{ route('dashboard') }}"
                        class="{{ !$sidebarOpen ? 'mx-auto' : '' }} flex flex-col justify-center items-center text-center">
                        @if ($sidebarOpen)
                            <img src="{{ asset('images/logo.png') }}" alt="Logo Aplikasi" class="h-32 w-auto">
                            <span class="text-xl font-bold text-wrap leading-tight -mt-6">Sistem Toko Olahraga</span>
                        @else
                            <img src="{{ asset('images/logo.png') }}" alt="Logo Ringkas" class="h-12 w-auto">
                        @endif
                    </a>
                </div>

                {{-- Navigasi utama --}}
                <nav class="space-y-1 w-full px-2">
                    @if (Auth::check())
                        {{-- === NAVIGASI UNTUK ADMIN PUSAT === --}}
                        @if (Auth::user()->isAdminPusat())
                            <a href="{{ route('admin-pusat.dashboard') }}" title="Dashboard"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('admin-pusat.dashboard') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-home class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Dashboard</span>
                            </a>
                            <a href="{{ route('admin-pusat.management.users') }}" title="Manajemen Pengguna"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('admin-pusat.management.users') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-user-group class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Manajemen
                                    Pengguna</span>
                            </a>
                            <a href="{{ route('admin-pusat.management.products') }}" title="Manajemen Produk"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('admin-pusat.management.products') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-cube class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Manajemen Produk</span>
                            </a>
                            <a href="{{ route('admin-pusat.management.stocks') }}" title="Manajemen Stok"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('admin-pusat.management.stocks') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-archive-box class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Manajemen Stok</span>
                            </a>
                            <a href="{{ route('admin-pusat.purchases.index') }}" title="Manajemen Pembelian"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('admin-pusat.purchases.*') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-arrow-up-on-square-stack
                                    class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Manajemen
                                    Pembelian</span>
                            </a>
                            <a href="{{ route('admin-pusat.reports.financial') }}" title="Laporan Keuangan"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('admin-pusat.reports.financial') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-chart-pie class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Laporan Keuangan</span>
                            </a>
                            <a href="{{ route('admin-pusat.forecasting.demand') }}" title="Peramalan Permintaan"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('admin-pusat.forecasting.demand') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-presentation-chart-line
                                    class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Peramalan
                                    Permintaan</span>
                            </a>

                            {{-- === NAVIGASI UNTUK MANAJER PUSAT (KODE YANG DIPERBAIKI) === --}}
                        @elseif (Auth::user()->isManajerPusat())
                            <a href="{{ route('manajer-pusat.dashboard') }}" title="Dashboard"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('manajer-pusat.dashboard') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-home class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Dashboard</span>
                            </a>
                            <a href="{{ route('manajer-pusat.stok.view') }}" title="Lihat Stok"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('manajer-pusat.stok.view') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-archive-box class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Lihat Stok</span>
                            </a>
                            <a href="{{ route('manajer-pusat.ramalan.view') }}" title="Lihat Ramalan"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('manajer-pusat.ramalan.view') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-presentation-chart-line
                                    class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Lihat Ramalan</span>
                            </a>
                            <a href="{{ route('manajer-pusat.laporan.keuangan') }}" title="Laporan Keuangan"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('manajer-pusat.laporan.keuangan') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-chart-pie class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Laporan Keuangan</span>
                            </a>

                            {{-- === NAVIGASI UNTUK MANAJER CABANG === --}}
                        @elseif (Auth::user()->isManajerCabang())
                            <a href="{{ route('manajer-cabang.dashboard') }}" title="Dashboard Cabang"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('manajer-cabang.dashboard') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-home class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Dashboard</span>
                            </a>
                            <a href="{{ route('manajer-cabang.stok.view') }}" title="Stok Cabang"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('manajer-cabang.stok.view') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-archive-box class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Stok Cabang</span>
                            </a>
                            <a href="{{ route('manajer-cabang.ramalan.view') }}" title="Ramalan Cabang"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('manajer-cabang.ramalan.view') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-presentation-chart-line
                                    class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Ramalan Cabang</span>
                            </a>
                            <a href="{{ route('manajer-cabang.laporan.keuangan') }}" title="Laporan Keuangan Cabang"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('manajer-cabang.laporan.keuangan') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-chart-pie class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Laporan Keuangan</span>
                            </a>

                        {{-- === NAVIGASI UNTUK ADMIN CABANG === --}}
                        @elseif (Auth::user()->isAdminCabang())
                            <a href="{{ route('admin-cabang.dashboard') }}" title="Dashboard Cabang"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('admin-cabang.dashboard') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-home class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Dashboard</span>
                            </a>
                            <a href="{{ route('admin-cabang.stok.manage') }}" title="Manajemen Stok"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('admin-cabang.stok.manage') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-archive-box class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Manajemen Stok</span>
                            </a>
                            <a href="{{ route('admin-cabang.pengguna.manage') }}" title="Manajemen Pengguna"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('admin-cabang.pengguna.manage') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-user-group class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Manajemen
                                    User</span>
                            </a>
                            <a href="{{ route('admin-cabang.laporan.keuangan') }}" title="Laporan Keuangan"
                                class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 {{ request()->routeIs('admin-cabang.laporan.keuangan') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                                <x-heroicon-o-chart-pie class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                                <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Laporan
                                    Keuangan</span>
                            </a>
                        @endif
                    @endif
                </nav>
            </div>

            {{-- User Info & Logout --}}
            <div
                class="mt-auto border-t border-orange-600 {{ $sidebarOpen ? 'p-4' : 'p-2 pt-4 flex flex-col items-center' }}">
                @if (Auth::check())
                    @if ($sidebarOpen)
                        <div class="flex items-center mb-3">
                            <img class="h-10 w-10 rounded-full object-cover"
                                src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-orange-200">{{ Str::title(Auth::user()->email) }}</p>
                            </div>
                        </div>
                    @endif
                @endif
                <div class="mt-1 space-y-1 w-full px-2">
                    <a href="{{ route('profile.show') }}" title="Profil"
                        class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group transition-colors duration-150 w-full {{ request()->routeIs('profile.show') ? 'bg-orange-800' : '' }} {{ !$sidebarOpen ? 'justify-center' : '' }}">
                        <x-heroicon-o-user-circle class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                        <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Profil</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" x-data class="w-full">
                        @csrf
                        <a href="{{ route('logout') }}" @click.prevent="$root.submit();" title="Logout"
                            class="flex items-center py-2.5 px-3 rounded-md hover:bg-orange-600 group w-full transition-colors duration-150 {{ !$sidebarOpen ? 'justify-center' : '' }}">
                            <x-heroicon-o-arrow-left-on-rectangle
                                class="h-6 w-6 shrink-0 {{ $sidebarOpen ? 'mr-3' : '' }}" />
                            <span class="{{ $sidebarOpen ? 'inline' : 'hidden' }} truncate">Logout</span>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </aside>
</div>
