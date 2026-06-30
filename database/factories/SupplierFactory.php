<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'business_id' => 1,
            'name' => fake()->company(),
            'name_ar' => fake()->company(),
            'tax_id' => fake()->numerify('############'),
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => 'Riyadh',
            'country' => 'SA',
            'type' => fake()->randomElement(['customer', 'vendor', 'both']),
            'credit_limit' => fake()->numberBetween(10000, 100000),
            'current_balance' => 0,
            'payment_terms' => fake()->word(),
            'status' => 'active',
        ];
    }
}
