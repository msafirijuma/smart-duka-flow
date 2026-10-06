<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $shopId = session('current_shop_id');
    $q = trim((string) $request->get('q', ''));

    $sales = Sale::with(['user', 'customer'])
        ->where('shop_id', $shopId)
        ->where('status', 'completed')
        ->when($q !== '', function ($query) use ($q) {
            $like = '%' . $q . '%';

            $query->where(function ($qry) use ($like, $q) {
                $qry->where('invoice_number', 'like', $like)
                    ->orWhere('payment_method', 'like', $like)
                    ->orWhere('total', 'like', $like)
                    ->orWhere('amount_paid', 'like', $like)
                    ->orWhereHas('user', function ($u) use ($like) {
                        $u->where('name', 'like', $like);
                    })
                    ->orWhereHas('customer', function ($c) use ($like) {
                        $c->where('name', 'like', $like)
                          ->orWhere('phone', 'like', $like);
                    })
                    ->orWhereHas('items', function ($i) use ($like) {
                        $i->where('product_name', 'like', $like)
                          // kama una product relation:
                          ->orWhereHas('product', function ($p) use ($like) {
                              $p->where('name', 'like', $like)
                                ->orWhere('sku', 'like', $like)
                                ->orWhere('barcode', 'like', $like);
                          });
                    });
            });
        })
        ->when($request->filled('from'), fn ($qry) =>
            $qry->whereDate('created_at', '>=', $request->from)
        )
        ->when($request->filled('to'), fn ($qry) =>
            $qry->whereDate('created_at', '<=', $request->to)
        )
        ->when($request->filled('payment_method'), fn ($qry) =>
            $qry->where('payment_method', $request->payment_method)
        )
        ->latest()
        ->paginate(20)
        ->withQueryString();

        // Summary
        $totalSales = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->sum('total');

        $todaySales = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');

        return view('sales.index', compact('sales', 'q', 'totalSales', 'todaySales'));
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

    // Receipt
    public function receipt(Sale $sale)
    {
        if ($sale->shop_id != session('current_shop_id')) {
            abort(403);
        }

        $sale->load(['items', 'user', 'customer', 'shop']);

        return view('sales.receipt', compact('sale'));
    }
}