<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phan_quyens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chuc_vu_id')->constrained('chuc_vus')->onDelete('cascade');
            $table->foreignId('chuc_nang_id')->constrained('chuc_nangs')->onDelete('cascade');
            $table->timestamps();

            // Only allow one specific chuc_nang per chuc_vu to prevent duplicate mappings
            $table->unique(['chuc_vu_id', 'chuc_nang_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phan_quyens');
    }
};
