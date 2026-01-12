<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function index()
    {
        // Check authorization - middleware auth sudah handle authentication
        if (!Gate::allows('access-cashier')) {
            abort(403, 'Anda tidak memiliki akses ke halaman kasir');
        }

        return view('cashier.index');
    }

    // search product by barcode or code
    public function find(Request $request)
    {
        if (!Gate::allows('access-cashier')) {
            abort(403);
        }

        $q = trim($request->input('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $products = Product::where('barcode', 'like', "%{$q}%")
            ->orWhere('code', 'like', "%{$q}%")
            ->orWhere('name', 'like', "%{$q}%")
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    // create transaction
    public function store(Request $request)
    {
        if (!\Illuminate\Support\Facades\Auth::check() || !Gate::allows('create-transactions')) {
            abort(403);
        }

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        // Validate availability before creating transaction
        foreach ($data['items'] as $it) {
            $productCheck = Product::findOrFail($it['product_id']);
            $qtyCheck = (int)$it['quantity'];
            if ($productCheck->stock < $qtyCheck) {
                return response()->json(['error' => 'Stok tidak cukup untuk produk: ' . $productCheck->name], 422);
            }
        }

        return DB::transaction(function () use ($data) {
            $user = \Illuminate\Support\Facades\Auth::user();

            $tx = Transaction::create([
                'user_id' => $user->id,
                'total' => 0,
                'paid_amount' => $data['paid_amount'],
                'change' => 0,
                'total_cost' => 0,
                'total_profit' => 0,
                'payment_method' => 'cash',
            ]);

            $total = 0;
            $totalCost = 0;

            foreach ($data['items'] as $it) {
                $product = Product::findOrFail($it['product_id']);
                $qty = (int)$it['quantity'];
                $sell = $product->sell_price;
                $cost = $product->cost_price;
                $itemTotal = $qty * $sell;
                $itemProfit = $qty * ($sell - $cost);

                TransactionItem::create([
                    'transaction_id' => $tx->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'sell_price' => $sell,
                    'cost_price' => $cost,
                    'item_total' => $itemTotal,
                    'item_profit' => $itemProfit,
                ]);

                $product->decrement('stock', $qty);

                $total += $itemTotal;
                $totalCost += $qty * $cost;
            }

            $paid = $tx->paid_amount;
            $tx->update([
                'total' => $total,
                'change' => $paid - $total,
                'total_cost' => $totalCost,
                'total_profit' => $total - $totalCost,
            ]);

            return response()->json($tx->load('items'));
        });
    }
}
