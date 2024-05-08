<?php

namespace Database\Factories;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditFactory extends Factory
{
    protected $model = Audit::class;

    public function definition(): array
    {
        $plannedDate =  now()->addMonths($this->faker->numberBetween(1, 12));
        $plannedEndDate = $plannedDate->addMonths($this->faker->numberBetween(1, 3));
        $actualDate = now()->addMonths($this->faker->numberBetween(1, 12));
        return [
            'title' => $this->faker->realText(100),
            'description' => $this->faker->realText(200),
            'planned_start_date' => $plannedDate,
            'planned_end_date' => $plannedEndDate,
            'actual_start_date' => null,
            'actual_end_date' => null,
            'year' => $plannedDate->year,
        ];
    }
}
