<?php

namespace Database\Factories;

use App\Models\Action;
use App\Models\Finding;
use App\Models\FollowUp;
use App\Models\Observation;
use App\Models\Recommendation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActionFactory extends Factory
{
    protected $model = Action::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'observation_id' => Observation::factory(),
            'follow_up_id' => FollowUp::factory(),
            'finding_id' => Finding::factory(),
            'recommendation_id' => Recommendation::factory(),
        ];
    }
}
