<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Shops (no shop.selected required)
    Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
    Route::get('/shops/create', [ShopController::class, 'create'])->name('shops.create');
    Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
    Route::post('/shops/{shop}/switch', [ShopController::class, 'switch'])->name('shops.switch');

    Route::middleware('shop.selected')->group(function () {

        // ===== ALL ROLES =====
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}/payment', [CustomerController::class, 'paymentForm'])->name('customers.payment');
        Route::post('/customers/{customer}/payment', [CustomerController::class, 'recordPayment'])->name('customers.payment.store');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

        // ===== OWNER + MANAGER =====
        Route::middleware('role:owner,manager')->group(function () {

            // Customers manage (create BEFORE {customer} already handled above for show)
            Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
            Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
            Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
            Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

            // Suppliers — static routes BEFORE {supplier}
            Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
            Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
            Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
            Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
            Route::get('/suppliers/{supplier}/payment', [SupplierController::class, 'paymentForm'])->name('suppliers.payment');
            Route::post('/suppliers/{supplier}/payment', [SupplierController::class, 'recordPayment'])->name('suppliers.payment.store');
            Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
            Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');

            // Purchases — static BEFORE {purchase}
            Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
            Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
            Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
            Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

            Route::resource('products', ProductController::class)->except(['show']);
            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('expenses', ExpenseController::class)->except(['show']);

            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
            Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
            Route::get('/reports/expenses', [ReportController::class, 'expenses'])->name('reports.expenses');
            Route::get('/reports/profit', [ReportController::class, 'profit'])->name('reports.profit');
        });

        // ===== OWNER ONLY =====
        Route::middleware('role:owner')->group(function () {
            Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
            Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
            Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
            Route::get('/staff/{user}/edit', [StaffController::class, 'edit'])->name('staff.edit');
            Route::put('/staff/{user}', [StaffController::class, 'update'])->name('staff.update');
            Route::delete('/staff/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');

            Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
            Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        });
    });
});

require __DIR__.'/auth.php';