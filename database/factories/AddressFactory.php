<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Address;

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
