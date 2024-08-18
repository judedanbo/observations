<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle().' Department',
            'short_name' => $this->faker->regexify('[A-Za-z0-9]{10}'),
            'description' => $this->faker->realText(100),
        ];
    }
}
