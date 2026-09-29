<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseController;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Shops (hakuna shop.selected bado — kwa sababu user anaweza kuwa hana shop)
    Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
    Route::get('/shops/create', [ShopController::class, 'create'])->name('shops.create');
    Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
    Route::post('/shops/{shop}/switch', [ShopController::class, 'switch'])->name('shops.switch');

    // Routes zinazohitaji shop iwe selected
    Route::middleware('shop.selected')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Categories
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Products
        Route::resource('products', ProductController::class)->except(['show']);

        // Point of Sales (Pos)
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

        // Sales
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

        // customers
        Route::resource('customers', CustomerController::class);

        // Expenses
        Route::resource('expenses', ExpenseController::class)->except(['show']);

        // Customer Payments
        Route::get('/customers/{customer}/payment', [CustomerController::class, 'paymentForm'])->name('customers.payment');
        Route::post('/customers/{customer}/payment', [CustomerController::class, 'recordPayment'])->name('customers.payment.store');

        // ========== TEMPORARY ROUTES (for testing sidebar) ==========

    Route::get('/settings', function () {
        return view('temp', ['title' => 'Settings']);
    })->name('settings.index');
    // ========== END TEMPORARY ==========

    });
});

require __DIR__.'/auth.php';