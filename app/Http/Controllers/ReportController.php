<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\SaleItem;
use App\Models\Shop;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    // =========================================================
    // EXCEL EXPORTS
    // =========================================================

    public function exportSales(Request $request): StreamedResponse |RedirectResponse
    {
        if ($msg = (new PlanLimitService)->canExport()) {
            return back()->with('error', $msg);
        }

        $shopId = session('current_shop_id');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $sales = Sale::with(['user', 'customer'])
            ->where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->when($request->filled('payment_method'), fn ($q) => $q->where('payment_method', $request->payment_method))
            ->latest()
            ->get();

        $filename = "sales_{$from}_to_{$to}.csv";

        return response()->streamDownload(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel

            fputcsv($handle, ['Invoice', 'Date', 'Cashier', 'Customer', 'Subtotal', 'Discount', 'Total', 'Payment', 'Amount Paid']);

            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->invoice_number,
                    $sale->created_at->format('Y-m-d H:i'),
                    $sale->user->name ?? '',
                    $sale->customer->name ?? 'Walk-in',
                    $sale->subtotal,
                    $sale->discount,
                    $sale->total,
                    $sale->payment_method,
                    $sale->amount_paid,
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPurchases(Request $request): StreamedResponse |RedirectResponse
    {
        if ($msg = (new PlanLimitService)->canExport()) {
            return back()->with('error', $msg);
        }

        $shopId = session('current_shop_id');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $purchases = Purchase::with(['supplier', 'user'])
            ->where('shop_id', $shopId)
            ->whereDate('purchase_date', '>=', $from)
            ->whereDate('purchase_date', '<=', $to)
            ->latest('purchase_date')
            ->get();

        $filename = "purchases_{$from}_to_{$to}.csv";

        return response()->streamDownload(function () use ($purchases) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['Reference', 'Date', 'Supplier', 'Subtotal', 'Discount', 'Total', 'Payment', 'Amount Paid', 'By']);

            foreach ($purchases as $p) {
                fputcsv($handle, [
                    $p->reference,
                    $p->purchase_date->format('Y-m-d'),
                    $p->supplier->name ?? '',
                    $p->subtotal,
                    $p->discount,
                    $p->total,
                    $p->payment_method,
                    $p->amount_paid,
                    $p->user->name ?? '',
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportExpenses(Request $request): StreamedResponse |RedirectResponse
    {
        if ($msg = (new PlanLimitService)->canExport()) {
            return back()->with('error', $msg);
        }

        $shopId = session('current_shop_id');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $expenses = Expense::with('user')
            ->where('shop_id', $shopId)
            ->whereDate('expense_date', '>=', $from)
            ->whereDate('expense_date', '<=', $to)
            ->latest('expense_date')
            ->get();

        $filename = "expenses_{$from}_to_{$to}.csv";

        return response()->streamDownload(function () use ($expenses) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['Title', 'Category', 'Amount', 'Payment', 'Date', 'By', 'Description']);

            foreach ($expenses as $e) {
                fputcsv($handle, [
                    $e->title,
                    $e->category ?? '',
                    $e->amount,
                    $e->payment_method,
                    $e->expense_date->format('Y-m-d'),
                    $e->user->name ?? '',
                    $e->description ?? '',
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportProfit(Request $request): StreamedResponse |RedirectResponse
    {
        if ($msg = (new PlanLimitService)->canExport()) {
            return back()->with('error', $msg);
        }

        $shopId = session('current_shop_id');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $revenue = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->sum('total');

        $cogs = \App\Models\SaleItem::whereHas('sale', function ($q) use ($shopId, $from, $to) {
                $q->where('shop_id', $shopId)
                ->where('status', 'completed')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(sale_items.quantity * products.cost_price) as cogs')
            ->value('cogs') ?? 0;

        $expenses = Expense::where('shop_id', $shopId)
            ->whereDate('expense_date', '>=', $from)
            ->whereDate('expense_date', '<=', $to)
            ->sum('amount');

        $gross = $revenue - $cogs;
        $net = $gross - $expenses;

        $filename = "profit_{$from}_to_{$to}.csv";

        return response()->streamDownload(function () use ($from, $to, $revenue, $cogs, $expenses, $gross, $net) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['Metric', 'Amount (TZS)']);
            fputcsv($handle, ['Period From', $from]);
            fputcsv($handle, ['Period To', $to]);
            fputcsv($handle, ['Revenue (Sales)', $revenue]);
            fputcsv($handle, ['COGS', $cogs]);
            fputcsv($handle, ['Gross Profit', $gross]);
            fputcsv($handle, ['Expenses', $expenses]);
            fputcsv($handle, ['Net Profit', $net]);
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // =========================================================
    // PDF EXPORTS
    // =========================================================

    public function exportSalesPdf(Request $request)
    {
        if ($msg = (new PlanLimitService)->canExport()) {
            return back()->with('error', $msg);
        }

        $shopId = session('current_shop_id');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $shop = Shop::findOrFail($shopId);

        $sales = Sale::with(['user', 'customer'])
            ->where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->when($request->filled('payment_method'), fn ($q) =>
                $q->where('payment_method', $request->payment_method)
            )
            ->latest()
            ->get();

        $total = $sales->sum('total');

        $pdf = Pdf::loadView('reports.pdf.sales', compact('shop', 'sales', 'from', 'to', 'total'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("sales_{$from}_to_{$to}.pdf");
    }

    public function exportPurchasesPdf(Request $request)
    {
        if ($msg = (new PlanLimitService)->canExport()) {
            return back()->with('error', $msg);
        }

        $shopId = session('current_shop_id');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $shop = Shop::findOrFail($shopId);

        $purchases = Purchase::with(['supplier', 'user'])
            ->where('shop_id', $shopId)
            ->whereDate('purchase_date', '>=', $from)
            ->whereDate('purchase_date', '<=', $to)
            ->latest('purchase_date')
            ->get();

        $total = $purchases->sum('total');

        $pdf = Pdf::loadView('reports.pdf.purchases', compact('shop', 'purchases', 'from', 'to', 'total'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("purchases_{$from}_to_{$to}.pdf");
    }

    public function exportExpensesPdf(Request $request)
    {
        if ($msg = (new PlanLimitService)->canExport()) {
            return back()->with('error', $msg);
        }

        $shopId = session('current_shop_id');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $shop = Shop::findOrFail($shopId);

        $expenses = Expense::with('user')
            ->where('shop_id', $shopId)
            ->whereDate('expense_date', '>=', $from)
            ->whereDate('expense_date', '<=', $to)
            ->latest('expense_date')
            ->get();

        $total = $expenses->sum('amount');

        $pdf = Pdf::loadView('reports.pdf.expenses', compact('shop', 'expenses', 'from', 'to', 'total'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("expenses_{$from}_to_{$to}.pdf");
    }

    public function exportProfitPdf(Request $request)
    {
        if ($msg = (new PlanLimitService)->canExport()) {
            return back()->with('error', $msg);
        }

        $shopId = session('current_shop_id');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $shop = Shop::findOrFail($shopId);

        $revenue = Sale::where('shop_id', $shopId)
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->sum('total');

        $cogs = SaleItem::whereHas('sale', function ($q) use ($shopId, $from, $to) {
                $q->where('shop_id', $shopId)
                ->where('status', 'completed')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->selectRaw('COALESCE(SUM(sale_items.quantity * products.cost_price), 0) as cogs')
            ->value('cogs') ?? 0;

        $expensesTotal = Expense::where('shop_id', $shopId)
            ->whereDate('expense_date', '>=', $from)
            ->whereDate('expense_date', '<=', $to)
            ->sum('amount');

        $gross = $revenue - $cogs;
        $net = $gross - $expensesTotal;

        $pdf = Pdf::loadView('reports.pdf.profit', compact(
            'shop', 'from', 'to', 'revenue', 'cogs', 'expensesTotal', 'gross', 'net'
        ))->setPaper('a4', 'portrait');

        return $pdf->download("profit_{$from}_to_{$to}.pdf");
    }
}