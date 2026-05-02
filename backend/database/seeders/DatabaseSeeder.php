<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Schema::disableForeignKeyConstraints();
        $this->call([
            SmartContractSeeder::class,
            RolePermissionSeeder::class,
            PhongBanSeeder::class,
            NhanVienSeeder::class,
            MonHocSeeder::class,
            SinhVienSeeder::class,
            LopHocSeeder::class,
            BangDiemSeeder::class,
            DonViCapSeeder::class,
            ChungChiSeeder::class,
            DuAnSeeder::class,
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
