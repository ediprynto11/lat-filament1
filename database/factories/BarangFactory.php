<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            // Default hanya nama saja, field lain kosong/null
            'nama' => $this->faker->word(),
        ];
    }
}