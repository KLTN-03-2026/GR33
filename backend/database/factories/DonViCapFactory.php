<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DonViCapFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ma_don_vi'   => 'DV_' . strtoupper($this->faker->unique()->bothify('?????')),
            'ten_don_vi'  => 'Tổ chức ' . $this->faker->company(),
            'loai_don_vi' => $this->faker->randomElement(['truong_dai_hoc', 'to_chuc_quoc_te', 'doanh_nghiep', 'khac']),
        ];
    }
}
