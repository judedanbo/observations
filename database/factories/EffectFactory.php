<?php

namespace Database\Factories;

use App\Models\Effect;
use App\Models\Finding;
use Illuminate\Database\Eloquent\Factories\Factory;

class EffectFactory extends Factory
{
    protected $model = Effect::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'finding_id' => Finding::factory(),
        ];
    }
}
