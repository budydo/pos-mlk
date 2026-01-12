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

        return [
            'code' => $code,
            'barcode' => fake()->boolean(70) ? fake()->unique()->ean13() : null,
            'name' => fake()->words(2, true),
            'cost_price' => fake()->randomFloat(2, 1, 100),
            'sell_price' => fake()->randomFloat(2, 10, 200),
            'stock' => 0,
        ];
    }
}
