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
        $finding = Finding::inRandomOrder()->first()->id;

        return [
            'title' => $this->faker->realText(100),
            'description' => $this->faker->realText(500),
            'finding_id' => $finding,
        ];
    }
}
