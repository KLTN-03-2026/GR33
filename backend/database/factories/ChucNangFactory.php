<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChucNangFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ma_chuc_nang'  => 'CN_' . strtoupper($this->faker->unique()->bothify('?????')),
            'ten_chuc_nang' => 'Chức năng ' . $this->faker->words(3, true),
        ];
    }
}
