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
use App\Http\Controllers\StockController;
use App\Http\Controllers\DataExportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ShopController as AdminShopController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\ActivityLogController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Shops
    Route::get('/shops', [AdminShopController::class, 'index'])->name('shops.index');
    Route::get('/shops/{shop}', [AdminShopController::class, 'show'])->name('shops.show');
    Route::post('/shops/{shop}/toggle', [AdminShopController::class, 'toggle'])->name('shops.toggle');
    Route::put('/shops/{shop}/plan', [AdminShopController::class, 'updatePlan'])
    ->name('shops.update-plan');

    // Packages (Plans)
    Route::get('/plans', [AdminPlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/create', [AdminPlanController::class, 'create'])->name('plans.create');
    Route::post('/plans', [AdminPlanController::class, 'store'])->name('plans.store');
    Route::get('/plans/{plan}/edit', [AdminPlanController::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{plan}', [AdminPlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [AdminPlanController::class, 'destroy'])->name('plans.destroy');

    // Settings
    Route::get('/settings/edit', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // Activity logs
    Route::get('/activity', [ActivityLogController::class, 'index'])->name('activity.index');

    // User
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
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

            // Stock
            Route::get('/stock', [StockController::class, 'index'])->name('stock.index');

            // Product | Categories | Expenses
            Route::resource('products', ProductController::class);
            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('expenses', ExpenseController::class)->except(['show']);

            // Reports Excel
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
            Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
            Route::get('/reports/expenses', [ReportController::class, 'expenses'])->name('reports.expenses');
            Route::get('/reports/profit', [ReportController::class, 'profit'])->name('reports.profit');

            // Reports PDF
            Route::get('/reports/sales/export-pdf', [ReportController::class, 'exportSalesPdf'])->name('reports.sales.export-pdf');
            Route::get('/reports/purchases/export-pdf', [ReportController::class, 'exportPurchasesPdf'])->name('reports.purchases.export-pdf');
            Route::get('/reports/expenses/export-pdf', [ReportController::class, 'exportExpensesPdf'])->name('reports.expenses.export-pdf');
            Route::get('/reports/profit/export-pdf', [ReportController::class, 'exportProfitPdf'])->name('reports.profit.export-pdf');

            // Reports ---- Export
            Route::get('/reports/sales/export', [ReportController::class, 'exportSales'])->name('reports.sales.export');
            Route::get('/reports/purchases/export', [ReportController::class, 'exportPurchases'])->name('reports.purchases.export');
            Route::get('/reports/expenses/export', [ReportController::class, 'exportExpenses'])->name('reports.expenses.export');
            Route::get('/reports/profit/export', [ReportController::class, 'exportProfit'])->name('reports.profit.export');
        });

        // ===== OWNER ONLY =====
        Route::middleware('role:owner')->group(function () {
            Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
            Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
            Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
            Route::get('/staff/{user}/edit', [StaffController::class, 'edit'])->name('staff.edit');
            Route::put('/staff/{user}', [StaffController::class, 'update'])->name('staff.update');
            Route::delete('/staff/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');

            // Data Export
            Route::get('/settings/data-export', [DataExportController::class, 'index'])
                ->name('settings.data-export');
            Route::post('/settings/data-export', [DataExportController::class, 'download'])
                ->name('settings.data-export.download');

            Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
            Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        });

        // Customers
        Route::get('/customers/{customer}/payment', [CustomerController::class, 'paymentForm'])->name('customers.payment');
        Route::post('/customers/{customer}/payment', [CustomerController::class, 'recordPayment'])->name('customers.payment.store');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

    });
});

require __DIR__.'/auth.php';