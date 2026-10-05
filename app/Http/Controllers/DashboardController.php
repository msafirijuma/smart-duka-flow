<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Shop;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $shopId = session('current_shop_id');
        $shop   = Shop::with('plan')->findOrFail($shopId);

        $today = today();

        // Today's sales (all completed, incl. credit)
        $todaySales = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('total');

        // Collections = cash-like (not pure credit)
        $todayCollections = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', $today)
            ->whereIn('payment_method', ['cash', 'mpesa', 'bank', 'mixed'])
            ->sum('amount_paid'); // au sum total kulingana na logic yako

        // If amount_paid not reliable, approximate:
        // ->where('payment_method', '!=', 'credit')->sum('total');

        $todayCredit = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', $today)
            ->where('payment_method', 'credit')
            ->sum('total');

        $outstandingDebts = Customer::where('shop_id', $shopId)
            ->where('balance', '>', 0)
            ->sum('balance');

        $productsCount = Product::where('shop_id', $shopId)->count();

        $lowStockCount = Product::where('shop_id', $shopId)
            ->where(function ($q) {
                $q->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                  ->orWhere('stock_quantity', '<=', 0);
            })
            ->count();

        $customersCount = Customer::where('shop_id', $shopId)->count();

        $salesThisMonth = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // Last 7 days chart
        $chartLabels = [];
        $chartData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $chartLabels[] = $d->format('D d');
            $chartData[] = (float) Sale::where('shop_id', $shopId)
                ->where('status', 'completed')
                ->whereDate('created_at', $d)
                ->sum('total');
        }

        // -------- Last 7 days: Purchases, Expenses, Profit, New customers --------
        $purchaseChartData = [];
        $expenseChartData  = [];
        $profitChartData   = [];
        $customerChartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);

            $daySales = (float) Sale::where('shop_id', $shopId)
                ->where('status', 'completed')
                ->whereDate('created_at', $d)
                ->sum('total');

            $dayCogs = (float) SaleItem::whereHas('sale', function ($q) use ($shopId, $d) {
                    $q->where('shop_id', $shopId)
                    ->where('status', 'completed')
                    ->whereDate('created_at', $d);
                })
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->selectRaw('COALESCE(SUM(sale_items.quantity * products.cost_price), 0) as cogs')
                ->value('cogs');

            $dayPurchases = (float) Purchase::where('shop_id', $shopId)
                ->whereDate('purchase_date', $d)
                ->sum('total');

            $dayExpenses = (float) Expense::where('shop_id', $shopId)
                ->whereDate('expense_date', $d)
                ->sum('amount');

            $dayNewCustomers = (int) Customer::where('shop_id', $shopId)
                ->whereDate('created_at', $d)
                ->count();

            $purchaseChartData[] = $dayPurchases;
            $expenseChartData[]  = $dayExpenses;
            $profitChartData[]   = $daySales - $dayCogs - $dayExpenses; // approx net
            $customerChartData[] = $dayNewCustomers;
        }

        $lowStockItems = Product::where('shop_id', $shopId)
            ->where(function ($q) {
                $q->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                  ->orWhere('stock_quantity', '<=', 0);
            })
            ->orderBy('stock_quantity')
            ->take(6)
            ->get();

        $recentSales = Sale::with(['user', 'customer'])
            ->where('shop_id', $shopId)
            ->where('status', 'completed')
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'shop',
            'todaySales',
            'todayCollections',
            'todayCredit',
            'outstandingDebts',
            'productsCount',
            'lowStockCount',
            'customersCount',
            'salesThisMonth',
            'chartLabels',
            'chartData',
            'lowStockItems',
            'recentSales',
            'purchaseChartData',
            'expenseChartData',
            'profitChartData',
            'customerChartData',
        ));
    }
}