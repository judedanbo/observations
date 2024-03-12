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
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'planned_start_date' => $plannedDate,
            'planned_end_date' => $plannedEndDate,
            'actual_start_date' => $actualDate,
            'actual_end_date' => null,
            'year' => $plannedDate->year,
        ];
    }
}
