<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PhongBanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ma_phong_ban'  => 'PB_' . strtoupper($this->faker->unique()->bothify('?????')),
            'ten_phong_ban' => 'Phòng Khoa ' . $this->faker->words(2, true),
            'mo_ta'         => $this->faker->sentence(),
        ];
    }
}
