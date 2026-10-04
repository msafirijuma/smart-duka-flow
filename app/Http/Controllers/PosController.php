<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index()
    {
        $shopId = session('current_shop_id');

        $products = Product::where('shop_id', $shopId)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get([
                    'id', 'name', 'selling_price', 'stock_quantity',
                    'unit', 'barcode', 'sku',
                    'image',              
                    'low_stock_threshold' 
                ]);

        // $products = Product::where('shop_id', $shopId)
        //     ->where('is_active', true)
        //     ->where('stock_quantity', '>', 0) 
        //     ->orderBy('name')
        //     ->get();

        $customers = Customer::where('shop_id', $shopId)
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        return view('pos.index', compact('products', 'customers'));
    }

    public function search(Request $request)
    {
        $shopId = session('current_shop_id');
        $q = $request->get('q');

        $products = Product::where('shop_id', $shopId)
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('barcode', 'like', "%{$q}%")
                      ->orWhere('sku', 'like', "%{$q}%");
            })
            ->limit(15)
            ->get(['id', 'name', 'selling_price', 'stock_quantity', 'unit', 'barcode', 'sku']);

        return response()->json($products);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price'    => 'required|numeric|min:0',
            'payment_method'   => 'required|in:cash,mpesa,bank,credit,mixed',
            'amount_paid'      => 'required|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0',
            'customer_id'      => 'nullable|exists:customers,id',
        ]);
        
        // Extra rule: credit must have customer
        if ($request->payment_method === 'credit' && !$request->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a customer for credit sales.',
            ], 422);
        }

        $shopId = session('current_shop_id');

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $saleItems = [];

            foreach ($request->items as $item) {
                $product = Product::where('shop_id', $shopId)->findOrFail($item['id']);

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->stock_quantity}");
                }

                $lineTotal = $item['price'] * $item['quantity'];
                $subtotal += $lineTotal;

                $saleItems[] = [
                    'product'    => $product,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total'      => $lineTotal,
                ];
            }

            $discount = $request->discount ?? 0;
            $total = max(0, $subtotal - $discount);
            $amountPaid = $request->amount_paid;
            $change = max(0, $amountPaid - $total);

            // Create Sale
            $sale = Sale::create([
                'shop_id'        => $shopId,
                'user_id'        => auth()->id(),
                'customer_id'    => $request->customer_id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'tax'            => 0,
                'total'          => $total,
                'payment_method' => $request->payment_method,
                'amount_paid'    => $amountPaid,
                'change_amount'  => $change,
                'status'         => 'completed',
            ]);

            // Create Sale Items + Reduce Stock
            foreach ($saleItems as $item) {
                SaleItem::create([
                    'sale_id'      => $sale->id,
                    'product_id'   => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'unit_price'   => $item['unit_price'],
                    'quantity'     => $item['quantity'],
                    'discount'     => 0,
                    'total'        => $item['total'],
                ]);

                $item['product']->decrement('stock_quantity', $item['quantity']);
            }

            // If payment is credit, increase customer balance
            if ($request->payment_method === 'credit' && $request->customer_id) {
                $customer = \App\Models\Customer::where('shop_id', $shopId)
                    ->findOrFail($request->customer_id);

                // Optional: check credit limit
                if (($customer->balance + $total) > $customer->credit_limit) {
                    throw new \Exception("Customer has exceeded credit limit. Available: TZS " . number_format($customer->credit_limit - $customer->balance, 0));
                }

                $customer->increment('balance', $total);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully',
                'sale_id' => $sale->id,
                'invoice' => $sale->invoice_number,
                'change'  => $change,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}