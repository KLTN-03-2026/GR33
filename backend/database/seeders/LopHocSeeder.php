<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LopHoc;

class LopHocSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        LopHoc::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $monHocCSDL = \App\Models\MonHoc::where('ma_mon_hoc', 'MH_CSDL')->first();
        $monHocOOP  = \App\Models\MonHoc::where('ma_mon_hoc', 'MH_OOP')->first();
        $giangVien  = \App\Models\NhanVien::where('email', 'thanhtrung05@gmail.com')->first();

        if ($monHocCSDL && $giangVien) {
            LopHoc::create([
                'ma_lop_hoc'    => 'LHCSDLA1',
                'ten_lop_hoc'   => 'Lớp Cơ Sở Dữ Liệu - Sáng Thứ 2',
                'mon_hoc_id'    => $monHocCSDL->id,
                'giang_vien_id' => $giangVien->id,
                'nam_hoc'       => '2024-2025',
                'hoc_ky'        => '1',
                'trang_thai'    => 'da_ket_thuc', // Đã kết thúc
                'si_so'         => 1,
            ]);
        }

        if ($monHocOOP && $giangVien) {
            LopHoc::create([
                'ma_lop_hoc'    => 'LHOOPA1',
                'ten_lop_hoc'   => 'Lớp Lập Trình Hướng Đối Tượng - Chiều Thứ 4',
                'mon_hoc_id'    => $monHocOOP->id,
                'giang_vien_id' => $giangVien->id,
                'nam_hoc'       => '2024-2025',
                'hoc_ky'        => '1',
                'trang_thai'    => 'da_ket_thuc', // Đã kết thúc
                'si_so'         => 1,
            ]);
        }
    }
}
