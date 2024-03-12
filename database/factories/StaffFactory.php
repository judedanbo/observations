<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'staff_number' => $this->faker->regexify('[A-Za-z0-9]{9}'),
            'email' => $this->faker->safeEmail(),
        ];
    }
}
