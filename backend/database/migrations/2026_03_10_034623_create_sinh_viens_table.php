<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sinh_viens', function (Blueprint $table) {
            $table->id();
            $table->string('ma_sinh_vien')->unique();
            $table->string('ho_ten');
            $table->string('nganh_hoc');
            $table->string('mat_khau');
            $table->string('email')->unique();
            $table->integer('nam_bat_dau')->default(2022);
            $table->integer('so_nam_hoc')->default(4); // 4 for Cử nhân, 5 for Kỹ sư
            $table->string('so_dien_thoai')->nullable();
            $table->text('dia_chi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinh_viens');
    }
};
