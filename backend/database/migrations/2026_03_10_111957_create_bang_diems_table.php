<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bang_diems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_id')->constrained('lop_hocs')->onDelete('cascade');
            $table->foreignId('sinh_vien_id')->constrained('sinh_viens')->onDelete('cascade');
            
            $table->float('diem_qua_trinh')->nullable();
            $table->float('diem_cuoi_ky')->nullable();
            $table->float('diem_tong_ket')->nullable();
            
            $table->float('diem_he_4')->nullable();
            $table->string('diem_chu')->nullable();
            
            $table->date('ngay_vao_diem')->nullable();
            $table->integer('trang_thai')->default(0); // 0: Chưa đúc, 1: Đã đúc, 2: Chờ duyệt, 3: Đã chốt điểm
            $table->boolean('is_locked')->default(false);
            
            $table->timestamps();
            
            $table->unique(['sinh_vien_id', 'lop_hoc_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bang_diems');
    }
};
