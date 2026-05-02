<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SinhVien;
use App\Models\LopHoc;

class BangDiemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sinh_vien_id'   => SinhVien::inRandomOrder()->first()->id ?? SinhVien::factory(),
            'lop_hoc_id'     => LopHoc::inRandomOrder()->first()->id ?? LopHoc::factory(),
            'diem_qua_trinh' => null,
            'diem_cuoi_ky'   => null,
            'diem_tong_ket'  => null,
            'diem_he_4'      => null,
            'diem_chu'       => null,
            'ngay_vao_diem'  => null,
            'is_locked'      => false,
        ];
    }
}
