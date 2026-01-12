<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $product_code = $request->input('product_code');
        $product_name = $request->input('product_name');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = Transaction::select('*', DB::raw('total as total_amount'))
            ->withCount('items')
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($product_code) {
            $query->whereHas('items.product', fn($q) => $q->where('code', 'like', "%{$product_code}%"));
        }

        if ($product_name) {
            $query->whereHas('items.product', fn($q) => $q->where('name', 'like', "%{$product_name}%"));
        }

        if ($date_from) {
            $query->whereDate('created_at', '>=', $date_from);
        }

        if ($date_to) {
            $query->whereDate('created_at', '<=', $date_to);
        }

        $transactions = $query->paginate(10)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = Transaction::with('items.product', 'user')->findOrFail($id);
        return view('transactions.show', compact('transaction'));
    }
}
