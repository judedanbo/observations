<?php

namespace Database\Factories;

use App\Models\Audit;
use App\Models\Observation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObservationFactory extends Factory
{
    protected $model = Observation::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->realText(100),
            'criteria' => $this->faker->realText(500),
            'audit_id' => Audit::factory(),
        ];
    }
}
