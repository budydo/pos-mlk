<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $total = fake()->randomFloat(2, 10, 500);
        $paid = max($total, fake()->randomFloat(2, $total, $total + 100));

        return [
            'user_id' => User::factory(),
            'total' => $total,
            'paid_amount' => $paid,
            'change' => $paid - $total,
            'total_cost' => 0, // to be updated by seeder when items are attached
            'total_profit' => 0,
            'payment_method' => 'cash',
        ];
    }
}
