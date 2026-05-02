<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MonHocFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ma_mon_hoc'  => 'MH_' . strtoupper($this->faker->unique()->bothify('?????')),
            'ten_mon_hoc' => 'Môn ' . $this->faker->words(3, true),
            'so_tin_chi'  => $this->faker->numberBetween(1, 4),
            'mo_ta'       => $this->faker->sentence(),
        ];
    }
}
