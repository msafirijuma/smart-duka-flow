<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\PlanLimitService;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('shop_id', session('current_shop_id'))
            ->latest()
            ->paginate(15);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('shop_id', session('current_shop_id'))
            ->where('is_active', true)
            ->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'category_id'          => 'nullable|exists:categories,id',
            'sku'                  => 'nullable|string|max:100',
            'barcode'              => 'nullable|string|max:100',
            'cost_price'           => 'required|numeric|min:0',
            'selling_price'        => 'required|numeric|min:0',
            'stock_quantity'       => 'required|integer|min:0',
            'low_stock_threshold'  => 'required|integer|min:0',
            'unit'                 => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB
        ]);

        $data = [
            'shop_id'              => session('current_shop_id'),
            'category_id'          => $request->category_id,
            'name'                 => $request->name,
            'slug'                 => Str::slug($request->name) . '-' . Str::random(4),
            'sku'                  => $request->sku,
            'barcode'              => $request->barcode,
            'cost_price'           => $request->cost_price,
            'selling_price'        => $request->selling_price,
            'stock_quantity'       => $request->stock_quantity,
            'low_stock_threshold'  => $request->low_stock_threshold,
            'unit'                 => $request->unit,
            'description'          => $request->description,
            'is_active'            => true,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $this->authorizeShop($product);

        $categories = Category::where('shop_id', session('current_shop_id'))
            ->where('is_active', true)
            ->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeShop($product);

        $request->validate([
            'name'                 => 'required|string|max:255',
            'category_id'          => 'nullable|exists:categories,id',
            'sku'                  => 'nullable|string|max:100',
            'barcode'              => 'nullable|string|max:100',
            'cost_price'           => 'required|numeric|min:0',
            'selling_price'        => 'required|numeric|min:0',
            'stock_quantity'       => 'required|integer|min:0',
            'low_stock_threshold'  => 'required|integer|min:0',
            'unit'                 => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($msg = (new PlanLimitService)->canAddProduct()) {
            return back()->with('error', $msg)->withInput();
        }

        $data = $request->only([
            'category_id'          => $request->category_id,
            'name'                 => $request->name,
            'sku'                  => $request->sku,
            'barcode'              => $request->barcode,
            'cost_price'           => $request->cost_price,
            'selling_price'        => $request->selling_price,
            'stock_quantity'       => $request->stock_quantity,
            'low_stock_threshold'  => $request->low_stock_threshold,
            'unit'                 => $request->unit,
            'description'          => $request->description,
        ]);

        if ($request->hasFile('image')) {
            // Delete old
            if ($product->image && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function show(Product $product)
    {
        if ($product->shop_id != session('current_shop_id')) {
            abort(403);
        }

        $product->load('category');

        return view('products.show', compact('product'));
    }

    public function destroy(Product $product)
    {
        $this->authorizeShop($product);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        // activity
        ActivityLogger::log(
            'product.deleted',
            "Product {$product->name} deleted",
            null,
            $shopId
        );

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    private function authorizeShop($product)
    {
        if ($product->shop_id != session('current_shop_id')) {
            abort(403);
        }
    }
}