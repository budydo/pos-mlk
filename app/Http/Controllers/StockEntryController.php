<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockEntryController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $q = $request->input('q');
        $product_code = $request->input('product_code');
        $product_name = $request->input('product_name');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = \App\Models\StockEntry::with('product')->orderBy('created_at', 'desc');

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->whereHas('product', function($p) use ($q) {
                    $p->where('code', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%");
                })->orWhere('note', 'like', "%{$q}%");
            });
        }

        if ($product_code) {
            $query->whereHas('product', fn($p) => $p->where('code', 'like', "%{$product_code}%"));
        }

        if ($product_name) {
            $query->whereHas('product', fn($p) => $p->where('name', 'like', "%{$product_name}%"));
        }

        if ($date_from) {
            $query->whereDate('created_at', '>=', $date_from);
        }

        if ($date_to) {
            $query->whereDate('created_at', '<=', $date_to);
        }

        $entries = $query->paginate(10)->withQueryString();

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
        // Ensure sell_price stays above cost_price
        if ($product->sell_price <= $product->cost_price) {
            $margin = max(1, round($product->cost_price * 0.1, 2));
            $product->sell_price = round($product->cost_price + $margin, 2);
        }
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
            if ($oldProduct->sell_price <= $oldProduct->cost_price) {
                $margin = max(1, round($oldProduct->cost_price * 0.1, 2));
                $oldProduct->sell_price = round($oldProduct->cost_price + $margin, 2);
            }
            $oldProduct->save();
        }

        if ($newProduct) {
            $latest = $newProduct->stockEntries()->orderBy('created_at','desc')->value('purchase_price');
            $newProduct->cost_price = $latest ?? $newProduct->cost_price;
            if ($newProduct->sell_price <= $newProduct->cost_price) {
                $margin = max(1, round($newProduct->cost_price * 0.1, 2));
                $newProduct->sell_price = round($newProduct->cost_price + $margin, 2);
            }
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
        if ($product->sell_price <= $product->cost_price) {
            $margin = max(1, round($product->cost_price * 0.1, 2));
            $product->sell_price = round($product->cost_price + $margin, 2);
        }
        $product->save();

        return redirect()->route('stock-entries.index')->with('success','Entri stok dihapus');
    }
}
