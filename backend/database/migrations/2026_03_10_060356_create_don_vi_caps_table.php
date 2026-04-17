<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('don_vi_caps', function (Blueprint $table) {
            $table->id();
            $table->string('ma_don_vi')->unique();
            $table->string('ten_don_vi');
            $table->string('loai_don_vi')->default('khac'); // truong_dai_hoc, to_chuc_quoc_te, doanh_nghiep, khac
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('don_vi_caps');
    }
};
