<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Product::factory()->count(10)->create()->each(function ($product) {
            // Create a few stock entries for history and update product stock
            $entries = rand(1, 3);
            $total = 0;

            for ($i = 0; $i < $entries; $i++) {
                $qty = rand(5, 50);
                $price = rand(500, 5000) / 100; // example

                StockEntry::create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'purchase_price' => $price,
                ]);

                $total += $qty;
            }

            $product->update(['stock' => $total]);
        });
    }
}
