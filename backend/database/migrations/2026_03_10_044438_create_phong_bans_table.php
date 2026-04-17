<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phong_bans', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phong_ban')->unique();
            $table->string('ten_phong_ban');
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phong_bans');
    }
};
