<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Employee;

class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'surname' => fake()->regexify('[A-Za-z0-9]{100}'),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'hire_date' => fake()->date(),
            'salary' => fake()->randomFloat(0, 0, 9999999999.),
            'status' => fake()->randomElement(["active","inactive"]),
        ];
    }
}
