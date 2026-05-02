<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SinhVien>
 */
class SinhVienFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ma_sinh_vien' => $this->faker->unique()->bothify('SV######'),
            'ho_ten'       => $this->faker->name(),
            'nganh_hoc'    => $this->faker->randomElement(['Công nghệ thông tin', 'Kinh tế', 'Cơ điện tử', 'Tự động hóa', 'Ngôn ngữ Anh']),
            'email'        => $this->faker->unique()->safeEmail(),
            'mat_khau'     => \Illuminate\Support\Facades\Hash::make('sinhvien123'),
            'nam_bat_dau'  => $this->faker->numberBetween(2020, 2024),
            'so_nam_hoc'   => $this->faker->randomElement([4, 5]),
            'so_dien_thoai' => '09' . $this->faker->numerify('########'),
            'dia_chi'      => $this->faker->address(),
            'trang_thai'   => $this->faker->randomElement([0, 1, 2, 3]),
            'hinh_anh'     => 'https://img.freepik.com/vector-cao-cap/anh-vector-minh-hoa-mau-sac-hinh-dai-dien-nguoi-dung-bieu-tuong-ho-so-ca-nhan-mot-nguoi-co-dac-diem-khuon-mat-phu-hop-cho-anh-dai-dien-tren-mang-xa-hoi-bieu-tuong-trinh-bao-ve-man-hinh-va-lam-mau_719432-2106.jpg?semt=ais_hybrid&w=740&q=80',
        ];
    }
}
