<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $shopId = session('current_shop_id');

        if (!$shopId) {
            return redirect()->route('shops.create')
                ->with('error', 'Please create a shop first.');
        }

        // 1. Today's Sales (all payment methods)
        $todaySales = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');

        // 2. Today's Collections (cash, mpesa, bank only — real money in)
        $todayCollections = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->whereIn('payment_method', ['cash', 'mpesa', 'bank', 'mixed'])
            ->sum('amount_paid'); // or sum('total') if you prefer sale value of paid sales

        // Better for collections: sum of totals for non-credit sales
        $todayCollections = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->whereIn('payment_method', ['cash', 'mpesa', 'bank', 'mixed'])
            ->sum('total');

        // 3. Today's Credit Sales
        $todayCredit = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->where('payment_method', 'credit')
            ->sum('total');

        // 4. Outstanding Debts (all customers balance)
        $outstandingDebts = Customer::where('shop_id', $shopId)
            ->sum('balance');

        // Extra (keep existing)
        $totalProducts = Product::where('shop_id', $shopId)->count();

        $lowStock = Product::where('shop_id', $shopId)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();

        $recentSales = Sale::where('shop_id', $shopId)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'todayCollections',
            'todayCredit',
            'outstandingDebts',
            'totalProducts',
            'lowStock',
            'recentSales'
        ));
    }
}