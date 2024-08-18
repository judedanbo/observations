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
        $observation = Observation::inRandomOrder()->first()->id;

        return [
            'title' => $this->faker->realText(100),
            'description' => $this->faker->realText(500),
            'observation_id' => $observation,
        ];
    }
}
