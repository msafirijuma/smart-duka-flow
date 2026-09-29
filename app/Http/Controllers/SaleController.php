<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $shopId = session('current_shop_id');

        $query = Sale::with(['user', 'customer'])
            ->where('shop_id', $shopId)
            ->latest();

        // Optional filters
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $sales = $query->paginate(20)->withQueryString();

        // Summary
        $totalSales = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->sum('total');

        $todaySales = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');

        return view('sales.index', compact('sales', 'totalSales', 'todaySales'));
    }

    public function show(Sale $sale)
    {
        // Security: only same shop
        if ($sale->shop_id != session('current_shop_id')) {
            abort(403);
        }

        $sale->load(['items.product', 'user', 'customer']);

        return view('sales.show', compact('sale'));
    }
}