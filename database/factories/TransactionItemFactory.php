<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionItem>
 */
class TransactionItemFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::factory();

        return [
            'transaction_id' => Transaction::factory(),
            'product_id' => $product,
            'quantity' => $qty = fake()->numberBetween(1, 5),
            'sell_price' => $sell = fake()->randomFloat(2, 5, 100),
            'cost_price' => $cost = fake()->randomFloat(2, 1, $sell - 1) ?: 0,
            'item_total' => $qty * $sell,
            'item_profit' => $qty * ($sell - $cost),
        ];
    }
}
