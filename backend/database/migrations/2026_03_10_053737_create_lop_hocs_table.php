<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lop_hocs', function (Blueprint $table) {
            $table->id();
            $table->string('ma_lop_hoc')->unique();
            $table->string('ten_lop_hoc');
            $table->foreignId('mon_hoc_id')->constrained('mon_hocs')->onDelete('cascade');
            $table->foreignId('giang_vien_id')->nullable()->constrained('nhan_viens')->onDelete('set null');
            $table->string('nam_hoc');
            $table->integer('hoc_ky');
            $table->string('trang_thai')->default('dang_mo'); // dang_mo, da_ket_thuc
            $table->integer('si_so')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lop_hocs');
    }
};
