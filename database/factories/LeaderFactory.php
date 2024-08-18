<?php

namespace Database\Factories;

use App\Models\Leader;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaderFactory extends Factory
{
    protected $model = Leader::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-5 year', 'now');

        return [
            'staff_number' => $this->faker->regexify('[A-Za-z0-9]{7}'),
            'name' => $this->faker->name(),
            'title' => $this->faker->jobTitle(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => null,
        ];
    }
}
