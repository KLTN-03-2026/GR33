<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SinhVien;
use App\Models\LopHoc;
use App\Models\BangDiem;

class BangDiemSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        BangDiem::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $lam = SinhVien::where('ma_sinh_vien', 'SV202201')->first();
        $lopCsdl = LopHoc::where('ma_lop_hoc', 'LHCSDLA1')->first();
        $lopOop  = LopHoc::where('ma_lop_hoc', 'LHOOPA1')->first();

        // 1. Nhập điểm CSDL cho Lâm (Giỏi - A)
        if ($lam && $lopCsdl) {
            BangDiem::create([
                'sinh_vien_id'   => $lam->id,
                'lop_hoc_id'     => $lopCsdl->id,
                'diem_qua_trinh' => 8.5,
                'diem_cuoi_ky'   => 9.0,
                'diem_tong_ket'  => 8.8,
                'diem_he_4'      => 4.0,
                'diem_chu'       => 'A',
                'ngay_vao_diem'  => now(),
                'is_locked'      => false,
                'trang_thai'     => BangDiem::STATUS_NOT_MINTED,
            ]);
        }

        // 2. Nhập điểm OOP cho Lâm (Khá - B+)
        if ($lam && $lopOop) {
            BangDiem::create([
                'sinh_vien_id'   => $lam->id,
                'lop_hoc_id'     => $lopOop->id,
                'diem_qua_trinh' => 7.5,
                'diem_cuoi_ky'   => 8.0,
                'diem_tong_ket'  => 7.8,
                'diem_he_4'      => 3.0,
                'diem_chu'       => 'B+',
                'ngay_vao_diem'  => now(),
                'is_locked'      => false,
                'trang_thai'     => BangDiem::STATUS_NOT_MINTED,
            ]);
        }
    }
}
