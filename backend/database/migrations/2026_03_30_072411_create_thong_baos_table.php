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
        Schema::create('thong_baos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nhan_vien_id')->nullable()->comment('ID Admin nhận thông báo');
            $table->unsignedBigInteger('sinh_vien_id')->nullable()->comment('ID Sinh viên nhận thông báo');
            $table->string('tieu_de');
            $table->text('noi_dung');
            $table->string('link')->nullable()->comment('Đường dẫn điều hướng');
            $table->boolean('is_read')->default(0)->comment('0: Chưa đọc, 1: Đã đọc');
            $table->string('loai')->default('info')->comment('info, success, warning, danger');
            $table->timestamps();

            // Khóa ngoại (nếu cần thiết, tuy nhiên đôi khi thông báo cho tất cả Admin nên không bắt buộc khóa ngoại ở đây)
            $table->foreign('nhan_vien_id')->references('id')->on('nhan_viens')->onDelete('cascade');
            $table->foreign('sinh_vien_id')->references('id')->on('sinh_viens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_baos');
    }
};
