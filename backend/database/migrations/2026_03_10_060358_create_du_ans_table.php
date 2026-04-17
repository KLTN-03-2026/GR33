<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('du_ans', function (Blueprint $table) {
            $table->id();
            $table->string('ma_du_an')->unique();
            $table->string('ten_du_an');
            $table->text('mo_ta')->nullable();
            $table->foreignId('sinh_vien_id')->constrained('sinh_viens')->onDelete('cascade');
            $table->string('link_du_an')->nullable();
            $table->integer('trang_thai')->default(0); // 0: Chưa đúc, 1: Đã đúc, 2: Chờ duyệt
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('du_ans');
    }
};
