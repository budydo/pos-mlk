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
        // Create 20 products
        Product::factory()->count(20)->create()->each(function ($product) {
            $total = 0;
            $lastPrice = $product->cost_price;

            // Create 100 stock entries per product
            for ($i = 0; $i < 100; $i++) {
                $qty = rand(1, 20);
                $price = rand(100, 5000) / 10; // example prices

                $entry = StockEntry::create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'purchase_price' => $price,
                ]);

                $total += $qty;
                $lastPrice = $price;
            }

            // Update product stock and cost_price to last purchase price
            $product->update(['stock' => $total, 'cost_price' => $lastPrice]);

            // Make sure sell_price remains greater than cost_price
            $product->refresh();
            if ($product->sell_price <= $product->cost_price) {
                // set a margin of 10% or at least 1 unit
                $margin = max(1, round($product->cost_price * 0.1, 2));
                $product->sell_price = round($product->cost_price + $margin, 2);
                $product->save();
            }
        });
    }
}
