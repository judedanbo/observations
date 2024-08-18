<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        $department = Department::inRandomOrder()->first()->id;

        return [
            'name' => $this->faker->jobTitle().' Unit',
            'description' => $this->faker->realText(100),
            'department_id' => $department,
        ];
    }
}
