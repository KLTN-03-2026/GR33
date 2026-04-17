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
        Schema::create('lich_su_giao_dichs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nft_van_bang_id')->constrained('nft_van_bangs')->onDelete('cascade');
            $table->foreignId('nguoi_thuc_hien_id')->nullable()->constrained('nhan_viens')->onDelete('set null');
            $table->string('hanh_dong');
            $table->string('gas_used')->nullable();
            $table->string('transaction_hash')->unique();
            $table->string('block_number')->nullable();
            $table->string('gas_price')->nullable();
            $table->integer('trang_thai')->default(1);
            $table->text('loi_chi_tiet')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_giao_dichs');
    }
};
