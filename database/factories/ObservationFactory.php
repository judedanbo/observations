<?php

namespace Database\Factories;

use App\Models\Observation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObservationFactory extends Factory
{
    protected $model = Observation::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'criteria' => $this->faker->paragraph(10),
        ];
    }
}
