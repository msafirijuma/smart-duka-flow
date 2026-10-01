<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $shopId = session('current_shop_id');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $query = Sale::with(['user', 'customer'])
            ->where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $sales = $query->latest()->paginate(30)->withQueryString();

        $base = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        $totalSales = (clone $base)->sum('total');
        $collections = (clone $base)->whereIn('payment_method', ['cash', 'mpesa', 'bank', 'mixed'])->sum('total');
        $creditSales = (clone $base)->where('payment_method', 'credit')->sum('total');
        $salesCount  = (clone $base)->count();

        return view('reports.sales', compact(
            'sales', 'from', 'to', 'totalSales', 'collections', 'creditSales', 'salesCount'
        ));
    }

    public function purchases(Request $request)
    {
        $shopId = session('current_shop_id');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $purchases = Purchase::with(['supplier', 'user'])
            ->where('shop_id', $shopId)
            ->whereDate('purchase_date', '>=', $from)
            ->whereDate('purchase_date', '<=', $to)
            ->latest('purchase_date')
            ->paginate(30)
            ->withQueryString();

        $totalPurchases = Purchase::where('shop_id', $shopId)
            ->whereDate('purchase_date', '>=', $from)
            ->whereDate('purchase_date', '<=', $to)
            ->sum('total');

        return view('reports.purchases', compact('purchases', 'from', 'to', 'totalPurchases'));
    }

    public function expenses(Request $request)
    {
        $shopId = session('current_shop_id');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $expenses = Expense::with('user')
            ->where('shop_id', $shopId)
            ->whereDate('expense_date', '>=', $from)
            ->whereDate('expense_date', '<=', $to)
            ->latest('expense_date')
            ->paginate(30)
            ->withQueryString();

        $totalExpenses = Expense::where('shop_id', $shopId)
            ->whereDate('expense_date', '>=', $from)
            ->whereDate('expense_date', '<=', $to)
            ->sum('amount');

        $byCategory = Expense::where('shop_id', $shopId)
            ->whereDate('expense_date', '>=', $from)
            ->whereDate('expense_date', '<=', $to)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        return view('reports.expenses', compact(
            'expenses', 'from', 'to', 'totalExpenses', 'byCategory'
        ));
    }

    public function profit(Request $request)
    {
        $shopId = session('current_shop_id');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        // Revenue = sales total
        $revenue = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->sum('total');

        // COGS approx = sum of (cost_price * qty) from sale items in period
        $cogs = SaleItem::whereHas('sale', function ($q) use ($shopId, $from, $to) {
                $q->where('shop_id', $shopId)
                  ->where('status', 'completed')
                  ->whereDate('created_at', '>=', $from)
                  ->whereDate('created_at', '<=', $to);
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(DB::raw('SUM(sale_items.quantity * products.cost_price) as cogs'))
            ->value('cogs') ?? 0;

        $expenses = Expense::where('shop_id', $shopId)
            ->whereDate('expense_date', '>=', $from)
            ->whereDate('expense_date', '<=', $to)
            ->sum('amount');

        $grossProfit = $revenue - $cogs;
        $netProfit   = $grossProfit - $expenses;

        return view('reports.profit', compact(
            'from', 'to', 'revenue', 'cogs', 'expenses', 'grossProfit', 'netProfit'
        ));
    }
}