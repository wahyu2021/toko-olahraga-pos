<?php

use App\Models\User;
use App\Livewire\PointOfSale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\ManajerPusat\ViewStock;
use App\Livewire\AdminPusat\PurchaseForm;
use App\Livewire\AdminPusat\PurchaseList;
use App\Livewire\AdminPusat\UserManagement;
use App\Livewire\ManajerPusat\ViewForecast;
use App\Livewire\AdminPusat\FinancialReport;
use App\Livewire\AdminPusat\StockManagement;
use App\Livewire\AdminPusat\DemandForecasting;
use App\Livewire\AdminPusat\ProductManagement;
use App\Livewire\ManajerCabang\ViewBranchStock;
use App\Livewire\AdminPusat\AdminPusatDashboard;
use App\Livewire\AdminCabang\AdminCabangDashboard;
use App\Livewire\AdminCabang\BranchUserManagement;
use App\Livewire\ManajerCabang\ViewBranchForecast;
use App\Livewire\AdminCabang\BranchStockManagement;
use App\Livewire\ManajerPusat\ManajerPusatDashboard;
use App\Livewire\ManajerCabang\BranchFinancialReport;
use App\Livewire\ManajerCabang\ManajerCabangDashboard;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', '/login');



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->isAdminPusat()) {
            return redirect()->route('admin-pusat.dashboard');
        } elseif ($user->isAdminCabang()) {
            return redirect()->route('admin-cabang.dashboard');
        } elseif ($user->isKasir()) {
            return redirect()->route('kasir.index');
        } elseif ($user->isManajerPusat()) {
            return redirect()->route('manajer-pusat.dashboard');
        } elseif ($user->isManajerCabang()) {
            return redirect()->route('manajer-cabang.dashboard');
        }
    })->name('dashboard');

    // Admin Pusat Route
    Route::middleware(['auth', 'role:' . User::ROLE_ADMIN_PUSAT])->prefix('/admin-pusat')->name('admin-pusat.')->group(function () {
        Route::get('/dashboard', AdminPusatDashboard::class)->name('dashboard');

        // Management
        Route::prefix('/management')->name('management.')->group(function () {
            Route::get('/users', UserManagement::class)->name('users');
            Route::get('/products', ProductManagement::class)->name('products');
            Route::get('/stocks', StockManagement::class)->name('stocks');
        });

        Route::get('/reports/financial', FinancialReport::class)->name('reports.financial');
        Route::get('/forecasting-demand', DemandForecasting::class)->name('forecasting.demand');
        Route::get('/pembelian', PurchaseList::class)->name('purchases.index');
        Route::get('/pembelian/baru', PurchaseForm::class)->name('purchases.create');
    });

    // Admin Pusat Route
    Route::middleware(['auth', 'role:' . User::ROLE_ADMIN_CABANG])->prefix('/admin-cabang')->name('admin-cabang.')->group(function () {
        Route::get('/dashboard', AdminCabangDashboard::class)->name('dashboard');
        Route::get('/stok', BranchStockManagement::class)->name('stok.manage');
        Route::get('/pengguna', BranchUserManagement::class)->name('pengguna.manage');
        Route::get('/laporan/keuangan', BranchFinancialReport::class)->name('laporan.keuangan');
    });



    // Kasir
    Route::middleware(['auth', 'role:' . User::ROLE_KASIR])->prefix('/kasir')->name('kasir.')->group(function () {
        Route::get('/', PointOfSale::class)->name('index');
    });

    // Manajer Pusat
    Route::middleware(['auth', 'role:' . User::ROLE_MANAJER_PUSAT])->prefix('/manajer-pusat')->name('manajer-pusat.')->group(function () {
        Route::get('/dashboard', ManajerPusatDashboard::class)->name('dashboard');
        Route::get('/stok', ViewStock::class)->name('stok.view');
        Route::get('/ramalan', ViewForecast::class)->name('ramalan.view');
        Route::get('/laporan/keuangan', FinancialReport::class)->name('laporan.keuangan');
    });

    // Manajer Cabang
    Route::middleware(['auth', 'role:' . User::ROLE_MANAJER_CABANG])->prefix('/manajer-cabang')->name('manajer-cabang.')->group(function () {
        Route::get('/dashboard', ManajerCabangDashboard::class)->name('dashboard');
        Route::get('/stok', ViewBranchStock::class)->name('stok.view');
        Route::get('/ramalan', ViewBranchForecast::class)->name('ramalan.view');
        Route::get('/laporan/keuangan', BranchFinancialReport::class)->name('laporan.keuangan');
    });
});
