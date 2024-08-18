<?php

namespace Database\Factories;

use App\Models\FollowUp;
use App\Models\Observation;
use Illuminate\Database\Eloquent\Factories\Factory;

class FollowUpFactory extends Factory
{
    protected $model = FollowUp::class;

    public function definition(): array
    {
        $observation = Observation::inRandomOrder()->with(['findings' => function ($query) {
            $query->with('recommendations');
        }])->get();
        $observationID = $observation->first()->id;
        $finding = $observation->first()->findings?->first();
        $findingId = $finding?->id;
        $recommendation = $finding?->recommendations?->first();
        $recommendationId = $recommendation?->id;

        return [
            'title' => $this->faker->realText(100),
            'description' => $this->faker->realText(200),
            'observation_id' => $observationID,
            'finding_id' => $findingId,
            'recommendation_id' => $recommendationId,
        ];
    }
}
