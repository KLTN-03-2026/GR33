<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DuAnSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        \App\Models\DuAn::truncate();
        Schema::enableForeignKeyConstraints();
    }
}
