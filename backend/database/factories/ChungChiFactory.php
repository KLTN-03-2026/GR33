<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SinhVien;
use App\Models\DonViCap;

class ChungChiFactory extends Factory
{
    public function definition(): array
    {
        $donViCapIds = DonViCap::pluck('id')->toArray();

        // Định nghĩa các loại chứng chỉ để map điểm và xếp loại cho hợp lý
        $chungChiHinhThuc = $this->faker->randomElement([
            ['loai' => 'ngoai_ngu', 'ten' => 'TOEIC', 'diem' => fn() => $this->faker->numberBetween(450, 990), 'rating' => function($diem) {
                if ($diem >= 900) return 'Xuất sắc';
                if ($diem >= 730) return 'Giỏi';
                if ($diem >= 600) return 'Khá';
                return 'Trung bình';
            }],
            ['loai' => 'ngoai_ngu', 'ten' => 'IELTS', 'diem' => fn() => $this->faker->randomFloat(1, 4.5, 9.0), 'rating' => function($diem) {
                if ($diem >= 8.0) return 'Xuất sắc';
                if ($diem >= 7.0) return 'Giỏi';
                if ($diem >= 6.0) return 'Khá';
                return 'Trung bình';
            }],
            ['loai' => 'tin_hoc', 'ten' => 'MOS', 'diem' => fn() => $this->faker->numberBetween(700, 1000), 'rating' => function($diem) {
                if ($diem >= 950) return 'Xuất sắc';
                if ($diem >= 850) return 'Giỏi';
                if ($diem >= 750) return 'Khá';
                return 'Trung bình';
            }],
            ['loai' => 'ky_nang', 'ten' => 'Kỹ năng mềm', 'diem' => fn() => $this->faker->randomElement(['Xuất sắc', 'Giỏi', 'Khá']), 'rating' => function($diem) {
                return $diem; // Điểm chính là xếp loại
            }],
        ]);

        $diemThucTe = $chungChiHinhThuc['diem']();
        $xepLoaiThucTe = $chungChiHinhThuc['rating']($diemThucTe);

        return [
            'ma_chung_chi'   => 'CC_' . strtoupper($this->faker->unique()->bothify('?????')),
            'ten_chung_chi'  => 'Chứng chỉ ' . $chungChiHinhThuc['ten'] . ' ' . $this->faker->words(1, true),
            'sinh_vien_id'   => SinhVien::inRandomOrder()->first()->id ?? SinhVien::factory(),
            'don_vi_cap_id'  => $this->faker->randomElement($donViCapIds),
            'loai_chung_chi' => $chungChiHinhThuc['loai'],
            'ngay_cap'       => $this->faker->date(),
            'ngay_het_han'   => $this->faker->boolean(50) ? $this->faker->dateTimeBetween('now', '+5 years')->format('Y-m-d') : null,
            'diem_so'        => (string) $diemThucTe,
            'xep_loai'       => $xepLoaiThucTe,
            'is_locked'      => false,
        ];
    }
}
