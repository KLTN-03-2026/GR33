<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonHoc;

class MonHocSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
         MonHoc::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $monHocs = [
            [
                'ma_mon_hoc' => 'MH_CSDL',
                'ten_mon_hoc' => 'Cơ Sở Dữ Liệu',
                'so_tin_chi'  => 3,
                'mo_ta'       => 'Môn học về thiết kế và quản trị cơ sở dữ liệu',
            ],
            [
                'ma_mon_hoc' => 'MH_OOP',
                'ten_mon_hoc' => 'Lập Trình Hướng Đối Tượng',
                'so_tin_chi'  => 3,
                'mo_ta'       => 'Môn học về tư duy lập trình hướng đối tượng (OOP)',
            ],
        ];

        foreach ($monHocs as $mh) {
            MonHoc::create($mh);
        }
    }
}
