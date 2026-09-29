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

        // Today's sales
        $todaySales = Sale::where('shop_id', $shopId)
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total');

        // Total products
        $totalProducts = Product::where('shop_id', $shopId)->count();

        // Low stock products
        $lowStock = Product::where('shop_id', $shopId)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();

        // Total customers
        $totalCustomers = Customer::where('shop_id', $shopId)->count();

        // Recent sales (last 5)
        $recentSales = Sale::where('shop_id', $shopId)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'totalProducts',
            'lowStock',
            'totalCustomers',
            'recentSales'
        ));
    }
}