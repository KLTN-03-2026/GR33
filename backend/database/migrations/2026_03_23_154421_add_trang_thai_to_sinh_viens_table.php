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
        Schema::table('sinh_viens', function (Blueprint $table) {
            $table->tinyInteger('trang_thai')->default(1)->comment('0: Nghỉ Học, 1: Đang Học, 2: Bảo Lưu, 3: Tốt Nghiệp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sinh_viens', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
};
