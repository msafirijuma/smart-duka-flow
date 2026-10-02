<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $shopId = session('current_shop_id');

        $query = Product::with('category')
            ->where('shop_id', $shopId)
            ->where('is_active', true);

        // Filter: all | low | out
        $filter = $request->get('filter', 'all');

        if ($filter === 'low') {
            $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                  ->where('stock_quantity', '>', 0);
        } elseif ($filter === 'out') {
            $query->where('stock_quantity', '<=', 0);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('stock_quantity')->paginate(20)->withQueryString();

        $categories = Category::where('shop_id', $shopId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Summary counts
        $base = Product::where('shop_id', $shopId)->where('is_active', true);

        $totalProducts = (clone $base)->count();
        $lowCount = (clone $base)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->count();
        $outCount = (clone $base)->where('stock_quantity', '<=', 0)->count();
        $okCount = $totalProducts - $lowCount - $outCount;

        return view('stock.index', compact(
            'products',
            'categories',
            'filter',
            'totalProducts',
            'lowCount',
            'outCount',
            'okCount'
        ));
    }
}