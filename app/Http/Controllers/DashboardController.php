<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // summary cards
        $today = Transaction::whereDate('created_at', now()->toDateString())->sum('total');
        $week = Transaction::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total');
        $month = Transaction::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total');

        // stock monitoring
        $lowStock = Product::whereBetween('stock', [1, 19])->count();
        $outStock = Product::where('stock', '<=', 0)->count();

        // monthly profit for last 6 months (only pemilik can see detailed chart)
        $monthly = collect();

        if (Gate::allows('view-reports')) {
            // Ambil transaksi dalam rentang 6 bulan terakhir, kemudian group di PHP
            $start = now()->startOfMonth()->subMonths(5);
            $transactions = Transaction::where('created_at', '>=', $start)->get(['created_at', 'total_profit']);

            // siapkan keys bulan (Y-m) untuk 6 bulan terakhir
            $months = [];
            for ($i = 0; $i < 6; $i++) {
                $key = $start->copy()->addMonths($i)->format('Y-m');
                $months[$key] = 0;
            }

            $grouped = $transactions->groupBy(function ($t) {
                return $t->created_at->format('Y-m');
            });

            foreach ($months as $key => $_) {
                $monthly[$key] = isset($grouped[$key]) ? $grouped[$key]->sum('total_profit') : 0;
            }
            $monthly = collect($monthly);
        }

        return view('dashboard.index', compact('today', 'week', 'month', 'lowStock', 'outStock', 'monthly', 'user'));
    }
}
