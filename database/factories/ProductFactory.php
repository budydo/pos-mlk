<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $code = strtoupper(fake()->unique()->bothify('PRD-####'));

        $cost = fake()->randomFloat(2, 1, 100);
        // ensure sell_price is always greater than cost_price by at least 1 unit
        $margin = fake()->randomFloat(2, 1, 20);
        $sell = round($cost + $margin, 2);

        return [
            'code' => $code,
            'barcode' => fake()->boolean(70) ? fake()->unique()->ean13() : null,
            'name' => fake()->words(2, true),
            'cost_price' => $cost,
            'sell_price' => $sell,
            'stock' => 0,
        ];
    }
}
