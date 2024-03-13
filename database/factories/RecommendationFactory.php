<?php

namespace Database\Factories;

use App\Models\Finding;
use App\Models\Recommendation;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecommendationFactory extends Factory
{
    protected $model = Recommendation::class;

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
