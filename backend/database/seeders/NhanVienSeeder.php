<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NhanVien;
use App\Models\ViNhanVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class NhanVienSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        ViNhanVien::truncate();
        NhanVien::truncate();
        Schema::enableForeignKeyConstraints();

        $password = Hash::make('admin123');
        $defaultImage = 'https://img.freepik.com/vector-cao-cap/anh-vector-minh-hoa-mau-sac-hinh-dai-dien-nguoi-dung-bieu-tuong-ho-so-ca-nhan-mot-nguoi-co-dac-diem-khuon-mat-phu-hop-cho-anh-dai-dien-tren-mang-xa-hoi-bieu-tuong-trinh-bao-ve-man-hinh-va-lam-mau_719432-2106.jpg?semt=ais_hybrid&w=740&q=80';

        // 1. Hoàng Văn Phong - Super Admin - CSE
        $phong = NhanVien::create([
            'ma_nhan_vien' => 'NV' . mt_rand(100000, 999999),
            'ho_ten' => 'Hoàng Văn Phong',
            'email' => 'vanphong92703@gmail.com',
            'mat_khau' => $password,
            'chuc_vu_id' => 1, // Super Admin
            'phong_ban_id' => 6, // Trung tâm Công nghệ Phần mềm (CSE)
            'so_dien_thoai' => '0987123456',
            'dia_chi' => 'Thanh Khê, Đà Nẵng',
            'hinh_anh' => 'https://res.cloudinary.com/dmciwnbzf/image/upload/v1774522027/503952570_4217213765268845_1845677478207844183_n.jpg',
        ]);

        // Ví cho Phong
        ViNhanVien::create([
            'nhan_vien_id' => $phong->id,
            'dia_chi_vi' => '0x76E8F0dcb3fb89ED8a386438E142ba5793CDFF74',
            'trang_thai' => 1,
        ]);

        // 2. Nguyễn Thanh Trung - Giảng viên - CSE
        $trung = NhanVien::create([
            'ma_nhan_vien' => 'NV' . mt_rand(100000, 999999),
            'ho_ten' => 'Nguyễn Thanh Trung',
            'email' => 'thanhtrung05@gmail.com',
            'mat_khau' => $password,
            'chuc_vu_id' => 6, // Giảng viên
            'phong_ban_id' => 6, // Trung tâm Công nghệ Phần mềm (CSE)
            'so_dien_thoai' => '0935885687',
            'dia_chi' => 'Quảng Nam, Đà Nẵng',
            'hinh_anh' => $defaultImage,
        ]);
    }
}
