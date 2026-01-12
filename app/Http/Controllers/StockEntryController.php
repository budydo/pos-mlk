<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockEntryController extends Controller
{
    public function index()
    {
        $entries = \App\Models\StockEntry::with('product')->latest()->get();
        return view('stock_entries.index', compact('entries'));
    }

    public function show($id)
    {
        $entry = \App\Models\StockEntry::findOrFail($id);
        return view('stock_entries.show', compact('entry'));
    }

    public function create()
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-stock');
        $products = \App\Models\Product::all();
        return view('stock_entries.create', compact('products'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-stock');

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $entry = \App\Models\StockEntry::create($data);
        $product = \App\Models\Product::find($data['product_id']);
        $product->increment('stock', $data['quantity']);

        // update product cost_price to last purchase price for synchronization
        $product->cost_price = $data['purchase_price'];
        $product->save();

        return redirect()->route('stock-entries.index')->with('success','Stok berhasil ditambahkan');
    }

    public function edit($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-stock');
        $entry = \App\Models\StockEntry::findOrFail($id);
        $products = \App\Models\Product::all();
        return view('stock_entries.edit', compact('entry','products'));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-stock');
        $entry = \App\Models\StockEntry::findOrFail($id);

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        // adjust product stock (reverse old, add new)
        if ($entry->product_id != $data['product_id'] || $entry->quantity != $data['quantity']) {
            \App\Models\Product::find($entry->product_id)->decrement('stock', $entry->quantity);
            \App\Models\Product::find($data['product_id'])->increment('stock', $data['quantity']);
        }

        $entry->update($data);

        // Recalculate cost_price for both affected products
        $oldProduct = \App\Models\Product::find($entry->product_id);
        $newProduct = \App\Models\Product::find($data['product_id']);

        if ($oldProduct) {
            $latest = $oldProduct->stockEntries()->orderBy('created_at','desc')->value('purchase_price');
            $oldProduct->cost_price = $latest ?? $oldProduct->cost_price;
            $oldProduct->save();
        }

        if ($newProduct) {
            $latest = $newProduct->stockEntries()->orderBy('created_at','desc')->value('purchase_price');
            $newProduct->cost_price = $latest ?? $newProduct->cost_price;
            $newProduct->save();
        }

        return redirect()->route('stock-entries.index')->with('success','Entri stok diperbarui');
    }

    public function destroy($id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-stock');
        $entry = \App\Models\StockEntry::findOrFail($id);
        $product = \App\Models\Product::find($entry->product_id);
        $product->decrement('stock', $entry->quantity);
        $entry->delete();

        // Recalculate product cost_price based on latest stock entry (if any)
        $latest = $product->stockEntries()->orderBy('created_at','desc')->value('purchase_price');
        $product->cost_price = $latest ?? $product->cost_price;
        $product->save();

        return redirect()->route('stock-entries.index')->with('success','Entri stok dihapus');
    }
}
