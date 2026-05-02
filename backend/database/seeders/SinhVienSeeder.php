<?php

namespace Database\Seeders;

use App\Models\SinhVien;
use App\Models\ViSinhVien;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\Hash;

class SinhVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        ViSinhVien::truncate();
        SinhVien::truncate();
        Schema::enableForeignKeyConstraints();

        $password = Hash::make('sinhvien123');
        $defaultImage = 'https://img.freepik.com/vector-cao-cap/anh-vector-minh-hoa-mau-sac-hinh-dai-dien-nguoi-dung-bieu-tuong-ho-so-ca-nhan-mot-nguoi-co-dac-diem-khuon-mat-phu-hop-cho-anh-dai-dien-tren-mang-xa-hoi-bieu-tuong-trinh-bao-ve-man-hinh-va-lam-mau_719432-2106.jpg?semt=ais_hybrid&w=740&q=80';

        // 1. Sinh viên mẫu Lưu Văn Lâm
        $lam = SinhVien::create([
            'ma_sinh_vien' => 'SV202201', 
            'ho_ten' => 'Lưu Văn Lâm',
            'email' => 'luuvanlam0207@gmail.com',
            'nganh_hoc' => 'Công Nghệ Thông Tin',
            'mat_khau' => $password,
            'nam_bat_dau' => 2022,
            'so_nam_hoc' => 4,
            'so_dien_thoai' => '0364501527',
            'dia_chi' => 'Duy Xuyên, Đà Nẵng',
            'trang_thai' => 1,
            'hinh_anh' => $defaultImage,
        ]);

        // Tạo ví mẫu cho Lâm
        ViSinhVien::create([
            'sinh_vien_id' => $lam->id,
            'dia_chi_vi' => '0x9d5ad34844ba28e5e6da28b45901c7c7b8f1c6a0',
            'trang_thai' => 1,
        ]);
    }
}
