<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MonHoc;
use App\Models\NhanVien;
use App\Models\ChucVu;

class LopHocFactory extends Factory
{
    public function definition(): array
    {
        $monHocIds = MonHoc::pluck('id')->toArray();
        
        // Find users with "Giảng viên" role to assign as teachers
        $giangVienRole = ChucVu::where('ten_chuc_vu', 'Giảng viên')->first();
        $giangVienIds = [];
        if ($giangVienRole) {
            $giangVienIds = NhanVien::where('chuc_vu_id', $giangVienRole->id)->pluck('id')->toArray();
        }
        
        // Fallback to any nhan_vien if no giang_vien found
        if (empty($giangVienIds)) {
            $giangVienIds = NhanVien::pluck('id')->toArray();
        }

        $namHoc = $this->faker->randomElement(['2024-2025', '2025-2026']);
        $trangThai = ($namHoc === '2025-2026') 
            ? $this->faker->randomElement(['sap_bat_dau', 'dang_mo', 'da_ket_thuc'])
            : 'da_ket_thuc';

        return [
            'ma_lop_hoc'    => 'LH_' . strtoupper($this->faker->unique()->bothify('?????')),
            'ten_lop_hoc'   => 'Lớp ' . $this->faker->words(2, true),
            'mon_hoc_id'    => $this->faker->randomElement($monHocIds),
            'giang_vien_id' => !empty($giangVienIds) ? $this->faker->randomElement($giangVienIds) : null,
            'nam_hoc'       => $namHoc,
            'hoc_ky'        => $this->faker->numberBetween(1, 3),
            'trang_thai'    => $trangThai,
            'si_so'         => $this->faker->numberBetween(0, 40),
        ];
    }
}
