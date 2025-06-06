<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Livewire\AdminPusat\UserManagement;
use App\Livewire\AdminPusat\FinancialReport;
use App\Livewire\AdminPusat\StockManagement;
use App\Livewire\AdminPusat\DemandForecasting;
use App\Livewire\AdminPusat\ProductManagement;
use App\Livewire\AdminPusat\AdminPusatDashboard;
use App\Livewire\PointOfSale;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

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

        } elseif ($user->isKasir()) {
            return redirect()->route('kasir.index');
        } else {
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
    });

    // Kasir
    Route::middleware(['auth', 'role:' . User::ROLE_KASIR])->prefix('/kasir')->name('kasir.')->group(function () {
        Route::get('/', PointOfSale::class)->name('index');
    });
});
