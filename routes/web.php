<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FraudDetectionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CatalogController;

Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/collection', [CatalogController::class, 'collection'])->name('collection');
Route::get('/product/{slug}', [CatalogController::class, 'show'])->name('products.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('fraud', [FraudDetectionController::class, 'index'])->name('fraud.index');
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/import-csv', [OrderController::class, 'importCsv'])->name('orders.import-csv');
    Route::get('orders/template-csv', [OrderController::class, 'downloadTemplate'])->name('orders.template-csv');
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::post('reports/import', [ReportsController::class, 'importMaster'])->name('reports.import');
    Route::get('reports/export', [ReportsController::class, 'export'])->name('reports.export');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // CMS Routes
    Route::resource('admin/products', \App\Http\Controllers\Admin\ProductController::class)->names('admin.products');
});

Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/users', [SettingsController::class, 'storeUser'])->name('settings.users.store');
    Route::patch('settings/users/{user}', [SettingsController::class, 'updateUser'])->name('settings.users.update');
});
