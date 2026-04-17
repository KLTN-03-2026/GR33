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
        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->text('hinh_anh')->nullable()->after('dia_chi');
        });
        Schema::table('sinh_viens', function (Blueprint $table) {
            $table->text('hinh_anh')->nullable()->after('dia_chi');
        });
    }

    public function down(): void
    {
        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->dropColumn('hinh_anh');
        });
        Schema::table('sinh_viens', function (Blueprint $table) {
            $table->dropColumn('hinh_anh');
        });
    }
};
