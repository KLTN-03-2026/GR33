<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chung_chis', function (Blueprint $table) {
            $table->id();
            $table->string('ma_chung_chi')->unique();
            $table->string('ten_chung_chi');
            $table->foreignId('sinh_vien_id')->constrained('sinh_viens')->onDelete('cascade');
            $table->unsignedBigInteger('don_vi_cap_id')->nullable(); // Bắt buộc nullable
            $table->string('ten_don_vi_cap_khac')->nullable();       // Bắt buộc nullable
            
            $table->foreign('don_vi_cap_id')->references('id')->on('don_vi_caps')->onDelete('set null');
            $table->string('loai_chung_chi')->default('khac'); // ngoai_ngu, tin_hoc, ky_nang, bang_cap, khac
            $table->date('ngay_cap')->nullable();
            $table->date('ngay_het_han')->nullable();
            $table->string('diem_so')->nullable(); // Có thể là "8.0", "900", "Xuất sắc"
            $table->string('xep_loai')->nullable();
            $table->string('file_dinh_kem')->nullable(); // Đường dẫn file PDF/JPG bản scan
            $table->integer('trang_thai')->default(0); // 0: Chưa đúc, 1: Đã đúc, 2: Chờ duyệt, 3: Thu hồi
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chung_chis');
    }
};
