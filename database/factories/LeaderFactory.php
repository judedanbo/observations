<?php

namespace Database\Factories;

use App\Models\Leader;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaderFactory extends Factory
{
    protected $model = Leader::class;

    public function definition(): array
    {
        return [
            'staff_number' => $this->faker->regexify('[A-Za-z0-9]{15}'),
            'name' => $this->faker->name(),
            'title' => $this->faker->sentence(4),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
        ];
    }
}
