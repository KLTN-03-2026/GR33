<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lop_hocs', function (Blueprint $table) {
            // Thay đổi mặc định cho tương lai
            $table->string('trang_thai')->default('sap_bat_dau')->change();
        });

        // Cập nhật chú thích hoặc logic nếu cần (Ở đây là string nên không cần enum update)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lop_hocs', function (Blueprint $table) {
            $table->string('trang_thai')->default('dang_mo')->change();
        });
    }
};
