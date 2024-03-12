<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'street' => $this->faker->streetName(),
            'city' => $this->faker->city(),
            'region' => $this->faker->regexify('[A-Za-z0-9]{3}'),
            'country' => $this->faker->country(),
        ];
    }
}
