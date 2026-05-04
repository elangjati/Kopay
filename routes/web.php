<?php

use App\Http\Controllers\Admin\WebhookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\KasirController;

// Landing page — publik
Route::get('/', fn() => view('landing'))->name('landing');

// Midtrans webhook — tidak perlu auth
Route::post('/webhook/midtrans', [WebhookController::class, 'midtrans'])->name('webhook.midtrans');

Route::prefix('admin')->name('admin.')->group(function () {

    // Auth
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {

        // Redirect dashboard lama ke kasir
        Route::get('/dashboard', [KasirController::class, 'index'])->name('dashboard');

        // Kasir
        Route::get('/kasir/create',                [KasirController::class, 'create'])->name('kasir.create');
        Route::post('/kasir',                      [KasirController::class, 'store'])->name('kasir.store');
        Route::get('/kasir/{order}/qris',          [KasirController::class, 'qris'])->name('kasir.qris');
        Route::get('/kasir/{order}/check-payment', [KasirController::class, 'checkPayment'])->name('kasir.checkPayment');

        // Orders
        Route::get('/orders/today',            [OrderController::class, 'today'])->name('orders.today');
        Route::get('/orders/{order}/edit',     [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}',          [OrderController::class, 'update'])->name('orders.update');
        Route::post('/orders/{order}/complete',[OrderController::class, 'complete'])->name('orders.complete');
        Route::get('/orders/{order}/receipt',  [OrderController::class, 'receipt'])->name('orders.receipt');
        Route::delete('/orders/{order}',       [OrderController::class, 'destroy'])->name('orders.destroy');

        // Menus
        Route::resource('menus', MenuController::class)->names('menus');

        // Reports — owner only
        Route::middleware('role:owner')->group(function () {
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        });
    });
});
