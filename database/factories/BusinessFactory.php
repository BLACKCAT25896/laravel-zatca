<?php

namespace Database\Factories;

use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'name' => fake()->company(),
            'name_ar' => fake()->company(),
            'tax_id' => fake()->unique()->numerify('############'),
            'crn' => fake()->unique()->numerify('###########'),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'address_ar' => fake()->address(),
            'city' => 'Riyadh',
            'postal_code' => fake()->postcode(),
            'country' => 'SA',
            'description' => fake()->sentence(),
            'industry_category' => fake()->word(),
            'is_vat_registered' => true,
            'vat_registration_date' => fake()->dateTimeThisYear(),
            'bank_name' => fake()->word(),
            'bank_account' => fake()->bankAccountNumber(),
            'bank_iban' => 'SA' . fake()->numerify('####################'),
            'status' => 'active',
        ];
    }
}
