<?php

namespace Database\Seeders;

use App\Models\ChungChi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ChungChiSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        ChungChi::truncate();
        Schema::enableForeignKeyConstraints();
    }
}
