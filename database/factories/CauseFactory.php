<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Cause;
use App\Models\Finding;

class CauseFactory extends Factory
{
    protected $model = Cause::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'finding_id' => Finding::factory(),
        ];
    }
}
