<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DonViCap;

class DonViCapSeeder extends Seeder
{
    public function run(): void
    {
        $donViCaps = [
            // 1. Nhóm các Trung tâm đào tạo/khảo thí trực thuộc trường
            [
                'ma_don_vi' => 'DTU_LTC', 
                'ten_don_vi' => 'Trung tâm Khảo thí Ngoại ngữ LTC - Đại học Duy Tân',
                'loai_don_vi' => 'TRUNG_TAM_TRUC_THUOC'
            ],
            [
                'ma_don_vi' => 'DTU_IT', 
                'ten_don_vi' => 'Trung tâm Tin học - Đại học Duy Tân',
                'loai_don_vi' => 'TRUNG_TAM_TRUC_THUOC'
            ],

            // 2. Nhóm các Tổ chức/Điểm thi Ủy quyền Quốc tế (Đối tác)
            [
                'ma_don_vi' => 'HSK_DANANG', 
                'ten_don_vi' => 'Điểm thi HSK Duy Tân - Đà Nẵng',
                'loai_don_vi' => 'DOI_TAC_QUOC_TE' // Điểm thi được Hanban Trung Quốc ủy quyền
            ],

            // 3. Nhóm các Khoa/Viện/Trường thành viên (Cấp giấy chứng nhận nội bộ/dự án)
            [
                'ma_don_vi' => 'KHOA_CNPM', 
                'ten_don_vi' => 'Khoa Công nghệ Phần mềm - Đại học Duy Tân',
                'loai_don_vi' => 'KHOA_VIEN'
            ],

            // 4. Khối Giáo dục Bắt buộc (Chuẩn đầu ra Quốc gia)
            [
                'ma_don_vi' => 'DTU_GDQP', 
                'ten_don_vi' => 'Trung tâm Giáo dục Quốc phòng và An ninh - Đại học Duy Tân',
                'loai_don_vi' => 'TRUNG_TAM_TRUC_THUOC'
            ],
            [
                'ma_don_vi' => 'DTU_GDTC', 
                'ten_don_vi' => 'Khoa Giáo dục Thể chất - Đại học Duy Tân',
                'loai_don_vi' => 'KHOA_VIEN'
            ],
        ];

        foreach ($donViCaps as $dvc) {
            DonViCap::updateOrCreate(['ma_don_vi' => $dvc['ma_don_vi']], $dvc);
        }
    }
}
