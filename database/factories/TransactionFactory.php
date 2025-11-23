<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_user_id' => null,
            'to_user_id' => null,
            'type' => fake()->randomElement(['transfer', 'deposit', 'reversal']),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'status' => 'completed',
            'description' => fake()->sentence(),
            'reversed_by_transaction_id' => null,
        ];
    }
}
