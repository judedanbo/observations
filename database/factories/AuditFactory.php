<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Audit;

class AuditFactory extends Factory
{
    protected $model = Audit::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'planned_start_date' => $this->faker->date(),
            'planned_end_date' => $this->faker->date(),
            'actual_start_date' => $this->faker->date(),
            'actual_end_date' => $this->faker->date(),
            'year' => $this->faker->numberBetween(-10000, 10000),
        ];
    }
}
