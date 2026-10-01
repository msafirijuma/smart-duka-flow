<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $shopId = session('current_shop_id');

        $query = Purchase::with(['supplier', 'user'])
            ->where('shop_id', $shopId)
            ->latest('purchase_date');

        if ($request->filled('from')) {
            $query->whereDate('purchase_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('purchase_date', '<=', $request->to);
        }

        $purchases = $query->paginate(20)->withQueryString();

        $monthTotal = Purchase::where('shop_id', $shopId)
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->sum('total');

        return view('purchases.index', compact('purchases', 'monthTotal'));
    }

    public function create()
    {
        $shopId = session('current_shop_id');

        $suppliers = Supplier::where('shop_id', $shopId)->orderBy('name')->get();
        $products = Product::where('shop_id', $shopId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'cost_price', 'stock_quantity', 'unit']);

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'purchase_date'      => 'required|date',
            'payment_method'     => 'required|in:cash,mpesa,bank,credit',
            'amount_paid'        => 'required|numeric|min:0',
            'discount'           => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string|max:500',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_cost'  => 'required|numeric|min:0',
        ]);

        $shopId = session('current_shop_id');
        $paymentMethod = $request->payment_method;

        if ($paymentMethod === 'credit' && !$request->supplier_id) {
            return back()->withErrors(['supplier_id' => 'Please select a supplier for credit purchases.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $lines = [];

            foreach ($request->items as $item) {
                $product = Product::where('shop_id', $shopId)->findOrFail($item['product_id']);
                $lineTotal = $item['unit_cost'] * $item['quantity'];
                $subtotal += $lineTotal;

                $lines[] = [
                    'product'   => $product,
                    'quantity'  => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total'     => $lineTotal,
                ];
            }

            $discount = $request->discount ?? 0;
            $total = max(0, $subtotal - $discount);
            $amountPaid = $request->amount_paid;

            if ($total <= 0) {
                throw new \Exception('Purchase total must be greater than zero.');
            }

            if (in_array($paymentMethod, ['cash', 'mpesa', 'bank']) && $amountPaid < $total) {
                throw new \Exception(
                    'Amount paid (TZS ' . number_format($amountPaid, 0) .
                    ') is less than total (TZS ' . number_format($total, 0) . ').'
                );
            }

            $purchase = Purchase::create([
                'shop_id'        => $shopId,
                'user_id'        => auth()->id(),
                'supplier_id'    => $request->supplier_id,
                'reference'      => 'PUR-' . strtoupper(Str::random(8)),
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total'          => $total,
                'payment_method' => $paymentMethod,
                'amount_paid'    => $amountPaid,
                'status'         => 'completed',
                'notes'          => $request->notes,
                'purchase_date'  => $request->purchase_date,
            ]);

            foreach ($lines as $line) {
                PurchaseItem::create([
                    'purchase_id'  => $purchase->id,
                    'product_id'   => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'unit_cost'    => $line['unit_cost'],
                    'quantity'     => $line['quantity'],
                    'total'        => $line['total'],
                ]);

                // Stock ↑
                $line['product']->increment('stock_quantity', $line['quantity']);

                // Optional: update cost_price to latest purchase cost
                $line['product']->update(['cost_price' => $line['unit_cost']]);
            }

            // Credit → increase supplier balance
            if ($paymentMethod === 'credit' && $request->supplier_id) {
                $supplier = Supplier::where('shop_id', $shopId)->findOrFail($request->supplier_id);
                $supplier->increment('balance', $total);
            }

            DB::commit();

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase recorded. Stock updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        if ($purchase->shop_id != session('current_shop_id')) {
            abort(403);
        }

        $purchase->load(['items', 'supplier', 'user']);

        return view('purchases.show', compact('purchase'));
    }
}