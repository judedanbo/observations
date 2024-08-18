<?php

namespace Database\Factories;

use App\Models\Cause;
use App\Models\Finding;
use Illuminate\Database\Eloquent\Factories\Factory;

class CauseFactory extends Factory
{
    protected $model = Cause::class;

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
