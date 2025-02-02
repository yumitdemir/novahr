<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\LeaveRequest;

class LeaveRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LeaveRequest::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'status' => fake()->regexify('[A-Za-z0-9]{100}'),
            'description' => fake()->text(),
            'leave_type' => fake()->regexify('[A-Za-z0-9]{100}'),
            'employee_id' => Employee::factory(),
        ];
    }
}
