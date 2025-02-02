<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Address;
use App\Models\Employee;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'fullAddress' => fake()->word(),
            'street' => fake()->streetName(),
            'city' => fake()->city(),
            'zip' => fake()->postcode(),
            'employee_id' => Employee::factory(),
        ];
    }
}
