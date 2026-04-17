<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nhan_viens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chuc_vu_id')->constrained('chuc_vus')->onDelete('cascade');
            $table->string('ma_nhan_vien')->unique();
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->string('mat_khau');
            $table->string('so_dien_thoai')->nullable();
            $table->text('dia_chi')->nullable();
            $table->foreignId('phong_ban_id')->nullable()->constrained('phong_bans')->onDelete('set null');
            $table->integer('trang_thai')->default(1)->comment('1: Hoạt động, 0: Tạm nghỉ, 2: Nghỉ việc');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhan_viens');
    }
};
