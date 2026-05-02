<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ChucVu;

class NhanVienFactory extends Factory
{
    public function definition(): array
    {
        $chucVuIds = ChucVu::pluck('id')->toArray();
        $phongBanIds = \App\Models\PhongBan::pluck('id')->toArray();

        return [
            'chuc_vu_id'   => $this->faker->randomElement($chucVuIds),
            'phong_ban_id' => $this->faker->randomElement($phongBanIds),
            'ma_nhan_vien' => $this->faker->unique()->bothify('NV######'),
            'ho_ten'       => $this->faker->name(),
            'so_dien_thoai' => '09' . $this->faker->numerify('########'),
            'dia_chi'      => $this->faker->address(),
            'email'        => $this->faker->unique()->safeEmail(),
            'mat_khau'     => \Illuminate\Support\Facades\Hash::make('password123'),
            'trang_thai'   => 1,
            'hinh_anh'     => 'https://img.freepik.com/vector-cao-cap/anh-vector-minh-hoa-mau-sac-hinh-dai-dien-nguoi-dung-bieu-tuong-ho-so-ca-nhan-mot-nguoi-co-dac-diem-khuon-mat-phu-hop-cho-anh-dai-dien-tren-mang-xa-hoi-bieu-tuong-trinh-bao-ve-man-hinh-va-lam-mau_719432-2106.jpg?semt=ais_hybrid&w=740&q=80',
        ];
    }
}
