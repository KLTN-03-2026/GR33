<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\PhongBan;

class PhongBanSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('phong_bans')->truncate();
        Schema::enableForeignKeyConstraints();

        $phongBans = [
            // Khối Lãnh đạo cấp cao (MỚI BỔ SUNG) - ID 1
            ['ma_phong_ban' => 'DTU_BGH', 'ten_phong_ban' => 'Ban Giám hiệu'],

            // Khối Hành chính - Đào tạo - ID 2, 3, 4
            ['ma_phong_ban' => 'DTU_PDT', 'ten_phong_ban' => 'Phòng Đào tạo Đại học & Sau Đại học'],
            ['ma_phong_ban' => 'DTU_CTSV', 'ten_phong_ban' => 'Phòng Công tác Sinh viên'],
            ['ma_phong_ban' => 'DTU_TCHC', 'ten_phong_ban' => 'Phòng Tổ chức - Hành chính'],
            
            // Khối Công nghệ - Khảo thí - ID 5, 6, 7
            ['ma_phong_ban' => 'DTU_CIT', 'ten_phong_ban' => 'Trung tâm Công nghệ Thông tin (CIT)'],
            ['ma_phong_ban' => 'DTU_CSE', 'ten_phong_ban' => 'Trung tâm Công nghệ Phần mềm (CSE)'],
            ['ma_phong_ban' => 'DTU_LTC', 'ten_phong_ban' => 'Trung tâm Khảo thí (LTC)'],

            // Khối Đào tạo (Các Trường/Khoa thành viên) - ID 8, 9, 10
            ['ma_phong_ban' => 'SCS', 'ten_phong_ban' => 'Trường Khoa học Máy tính (SCS)'],
            ['ma_phong_ban' => 'SBE', 'ten_phong_ban' => 'Trường Kinh tế (SBE)'],
            ['ma_phong_ban' => 'IS', 'ten_phong_ban' => 'Viện Đào tạo Quốc tế (IS)'],
        ];

        foreach ($phongBans as $pb) {
            PhongBan::create($pb);
        }
    }
}
