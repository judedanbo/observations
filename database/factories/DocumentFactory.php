<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->realText(100),
            'description' => $this->faker->realText(200),
            'file' => $this->faker->regexify('[A-Za-z0-9]{20}').'.'.$this->faker->fileExtension(),
        ];
    }
}
