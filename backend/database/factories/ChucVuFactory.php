<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChucVuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ten_chuc_vu' => $this->faker->unique()->jobTitle() . ' ' . $this->faker->unique()->randomNumber(4),
            'mo_ta'       => $this->faker->sentence(),
        ];
    }
}
