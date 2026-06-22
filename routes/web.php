<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\KasirController;

// Landing page + menu publik
Route::get('/', function () {
    $menus = \App\Models\Menu::where('is_available', true)
        ->orderBy('category')
        ->orderBy('name')
        ->get()
        ->groupBy('category');
    return view('landing', compact('menus'));
})->name('landing');

// /menu redirect ke landing
Route::get('/menu', fn() => redirect()->route('landing'))->name('menu');

Route::prefix('admin')->name('admin.')->group(function () {

    // Auth
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {

        // Dashboard
        Route::get('/dashboard', [KasirController::class, 'index'])->name('dashboard');

        // Kasir
        Route::get('/kasir/create',        [KasirController::class, 'create'])->name('kasir.create');
        Route::post('/kasir',              [KasirController::class, 'store'])->name('kasir.store');
        Route::get('/kasir/daily-report',  [KasirController::class, 'dailyReport'])->name('kasir.daily-report');

        // Orders
        Route::get('/orders/today',            [OrderController::class, 'today'])->name('orders.today');
        Route::get('/orders/history',          [OrderController::class, 'history'])->name('orders.history');
        
        // Trash (owner only) — harus sebelum parameterized route
        Route::middleware('role:owner')->group(function () {
            Route::get('/orders/trash/list',     [OrderController::class, 'trash'])->name('orders.trash');
        });

        Route::get('/orders/{order}/edit',     [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}',          [OrderController::class, 'update'])->name('orders.update');
        Route::post('/orders/{order}/complete',[OrderController::class, 'complete'])->name('orders.complete');
        Route::post('/orders/{order}/change-payment',[OrderController::class, 'changePayment'])->name('orders.change-payment');
        Route::get('/orders/{order}/receipt',  [OrderController::class, 'receipt'])->name('orders.receipt');

        // Delete & Restore orders — owner only
        Route::middleware('role:owner')->group(function () {
            Route::delete('/orders/{order}',   [OrderController::class, 'destroy'])->name('orders.destroy');
            Route::post('/orders/{order}/restore', [OrderController::class, 'restore'])->name('orders.restore');
        });

        // Menus
        Route::resource('menus', MenuController::class)->names('menus');

        // Reports — owner only
        Route::middleware('role:owner')->group(function () {
            Route::get('/reports',        [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        });
    });
});
