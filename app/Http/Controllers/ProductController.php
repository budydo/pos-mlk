<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = \App\Models\Product::all();
        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function create()
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-products');
        return view('products.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-products');

        $data = $request->validate([
            'code' => 'required|unique:products,code',
            'barcode' => 'nullable|unique:products,barcode',
            'name' => 'required|string',
            'cost_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        \App\Models\Product::create($data + ['stock' => $data['stock'] ?? 0]);

        return redirect()->route('products.index')->with('success','Produk berhasil dibuat');
    }

    public function edit($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-products');
        $product = \App\Models\Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-products');
        $product = \App\Models\Product::findOrFail($id);

        $data = $request->validate([
            'code' => 'required|unique:products,code,' . $product->id,
            'barcode' => 'nullable|unique:products,barcode,' . $product->id,
            'name' => 'required|string',
            'cost_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        $product->update($data + ['stock' => $data['stock'] ?? $product->stock]);

        return redirect()->route('products.index')->with('success','Produk diperbarui');
    }

    public function destroy($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-products');
        $product = \App\Models\Product::findOrFail($id);
        $product->delete();
        return redirect()->route('products.index')->with('success','Produk dihapus');
    }
}
