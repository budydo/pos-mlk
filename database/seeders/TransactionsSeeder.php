<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionsSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $cashiers = User::where('role', 'karyawan')->get();
        $products = Product::all();

        if ($cashiers->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Create 10 transactions
        for ($i = 0; $i < 10; $i++) {
            DB::transaction(function () use ($cashiers, $products, $i) {
                $date = now()->subDays($i)->subMinutes(rand(0, 1440));
                $user = $cashiers->random();

                // Ambil 2-4 produk acak untuk setiap transaksi
                $items = $products->random(rand(2, 4));

                $total = 0;
                $totalCost = 0;

                $tx = Transaction::create([
                    'user_id' => $user->id,
                    'total' => 0,
                    'paid_amount' => 0,
                    'change' => 0,
                    'total_cost' => 0,
                    'total_profit' => 0,
                    'payment_method' => 'cash',
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                foreach ($items as $product) {
                    // Jangan kurangi stok untuk demo, tapi masukkan quantity yang realistis
                    $qty = rand(1, 5);

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
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    $total += $itemTotal;
                    $totalCost += $qty * $cost;
                }

                if ($total == 0) {
                    $tx->delete();
                    return;
                }

                // Bayar dengan jumlah yang pas atau lebih
                $paid = $total + rand(0, 50000);

                $tx->update([
                    'total' => $total,
                    'paid_amount' => $paid,
                    'change' => $paid - $total,
                    'total_cost' => $totalCost,
                    'total_profit' => $total - $totalCost,
                ]);
            });
        }
    }
}

