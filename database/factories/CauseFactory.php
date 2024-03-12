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
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'finding_id' =>  $finding,
        ];
    }
}
