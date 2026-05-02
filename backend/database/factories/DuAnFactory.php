<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SinhVien;
use App\Models\NhanVien;
use App\Models\ChucVu;

class DuAnFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ma_du_an'           => 'DA_' . strtoupper($this->faker->unique()->bothify('?????')),
            'ten_du_an'          => 'Dự án ' . $this->faker->words(3, true),
            'mo_ta'              => $this->faker->paragraph(),
            'sinh_vien_id'       => SinhVien::inRandomOrder()->first()->id ?? SinhVien::factory(),
            'link_du_an'         => $this->faker->url(),
            'is_locked'          => false, // 20% records are locked => NO, FORCE FALSE NOW
        ];
    }
}
