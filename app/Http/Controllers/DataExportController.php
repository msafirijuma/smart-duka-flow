<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class DataExportController extends Controller
{
    public function index()
    {
        $shop = Shop::with('plan')->findOrFail(session('current_shop_id'));

        return view('settings.data-export', compact('shop'));
    }

    public function download(Request $request)
    {
        $shopId = session('current_shop_id');
        $shop = Shop::findOrFail($shopId);

        // Owner only
        $role = Auth::user()->shops()
            ->where('shops.id', $shopId)
            ->first()
            ?->pivot
            ?->role;

        if ($role !== 'owner' && !Auth::user()->isAdmin()) {
            abort(403, 'Only the shop owner can export full data.');
        }

        $filename = 'dukaflow-export-' . \Illuminate\Support\Str::slug($shop->name) . '-' . now()->format('Y-m-d') . '.zip';
        $tmpZip = tempnam(sys_get_temp_dir(), 'df_export_') . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Could not create export file.');
        }

        // 1) Shop summary
        $zip->addFromString('shop.json', json_encode([
            'exported_at' => now()->toIso8601String(),
            'shop' => [
                'name' => $shop->name,
                'phone' => $shop->phone,
                'email' => $shop->email,
                'address' => $shop->address,
                'plan' => $shop->plan?->name,
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 2) CSVs
        $zip->addFromString('products.csv', $this->csv(
            ['ID', 'Name', 'SKU', 'Barcode', 'Cost', 'Selling', 'Stock', 'Unit', 'Active'],
            Product::where('shop_id', $shopId)->get()->map(fn ($p) => [
                $p->id, $p->name, $p->sku, $p->barcode,
                $p->cost_price, $p->selling_price, $p->stock_quantity,
                $p->unit, $p->is_active ? 1 : 0,
            ])
        ));

        $zip->addFromString('customers.csv', $this->csv(
            ['ID', 'Name', 'Phone', 'Email', 'Balance', 'Credit Limit'],
            Customer::where('shop_id', $shopId)->get()->map(fn ($c) => [
                $c->id, $c->name, $c->phone, $c->email, $c->balance, $c->credit_limit,
            ])
        ));

        $zip->addFromString('sales.csv', $this->csv(
            ['ID', 'Invoice', 'Date', 'Total', 'Discount', 'Payment', 'Amount Paid', 'Customer ID'],
            Sale::where('shop_id', $shopId)->get()->map(fn ($s) => [
                $s->id, $s->invoice_number, $s->created_at, $s->total,
                $s->discount, $s->payment_method, $s->amount_paid, $s->customer_id,
            ])
        ));

        $zip->addFromString('sale_items.csv', $this->csv(
            ['Sale ID', 'Product ID', 'Product Name', 'Qty', 'Unit Price', 'Total'],
            SaleItem::whereHas('sale', fn ($q) => $q->where('shop_id', $shopId))
                ->get()
                ->map(fn ($i) => [
                    $i->sale_id, $i->product_id, $i->product_name ?? '',
                    $i->quantity, $i->unit_price, $i->total,
                ])
        ));

        $zip->addFromString('suppliers.csv', $this->csv(
            ['ID', 'Name', 'Phone', 'Email', 'Balance'],
            Supplier::where('shop_id', $shopId)->get()->map(fn ($s) => [
                $s->id, $s->name, $s->phone, $s->email, $s->balance,
            ])
        ));

        $zip->addFromString('purchases.csv', $this->csv(
            ['ID', 'Reference', 'Date', 'Total', 'Payment', 'Supplier ID'],
            Purchase::where('shop_id', $shopId)->get()->map(fn ($p) => [
                $p->id, $p->reference, $p->purchase_date, $p->total,
                $p->payment_method, $p->supplier_id,
            ])
        ));

        $zip->addFromString('expenses.csv', $this->csv(
            ['ID', 'Title', 'Category', 'Amount', 'Date', 'Payment'],
            Expense::where('shop_id', $shopId)->get()->map(fn ($e) => [
                $e->id, $e->title, $e->category, $e->amount,
                $e->expense_date, $e->payment_method,
            ])
        ));

        $zip->addFromString('README.txt',
            "DukaFlow data export\nShop: {$shop->name}\nDate: " . now()->toDateTimeString() . "\n\n" .
            "Files are CSV (open in Excel) plus shop.json.\nThis is a copy of your shop data for your records.\n"
        );

        $zip->close();

        // activity log
        ActivityLogger::log(
            'data.exported',
            "Shop data exported: {$shop->name}",
            $shop,
            $shop->id
        );

        return response()->download($tmpZip, $filename, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    private function csv(array $headers, $rows): string
    {
        $fh = fopen('php://temp', 'r+');
        // UTF-8 BOM for Excel
        fwrite($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($fh, $headers);
        foreach ($rows as $row) {
            fputcsv($fh, is_array($row) ? $row : $row->toArray());
        }
        rewind($fh);
        $content = stream_get_contents($fh);
        fclose($fh);
        return $content;
    }
}