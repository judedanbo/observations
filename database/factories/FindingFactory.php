<?php

namespace Database\Factories;

use App\Models\Finding;
use App\Models\Observation;
use Illuminate\Database\Eloquent\Factories\Factory;

class FindingFactory extends Factory
{
    protected $model = Finding::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(15),
            'observation_id' => Observation::factory(),
        ];
    }
}
