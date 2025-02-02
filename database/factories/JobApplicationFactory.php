<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\JobApplication;
use App\Models\JobOpening;

class JobApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JobApplication::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(["pending","accepted","rejected"]),
            'application_date' => fake()->date(),
            'name' => fake()->name(),
            'surname' => fake()->regexify('[A-Za-z0-9]{100}'),
            'cv' => fake()->text(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'linkedin' => fake()->word(),
            'location' => fake()->word(),
            'current_job_title' => fake()->word(),
            'current_employer' => fake()->word(),
            'years_of_experience' => fake()->numberBetween(-10000, 10000),
            'university' => fake()->word(),
            'certifications' => fake()->word(),
            'technical_skills' => fake()->word(),
            'soft_skills' => fake()->word(),
            'languages_spoken' => fake()->word(),
            'compatibility_rating' => fake()->word(),
            'job_opening_id' => JobOpening::factory(),
        ];
    }
}
