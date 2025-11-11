<?php

namespace Database\Factories;

use App\Models\Action;
use App\Models\FollowUp;
use App\Models\Observation;
use App\Models\Recommendation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActionFactory extends Factory
{
    protected $model = Action::class;

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
            'description' => $this->faker->realText(500),
            'observation_id' => $observationID,
            'finding_id' => $findingId,
            'follow_up_id' => null, // FollowUp::factory(),
            'recommendation_id' => $recommendationId, // Recommendation::factory(),
        ];
    }
}
