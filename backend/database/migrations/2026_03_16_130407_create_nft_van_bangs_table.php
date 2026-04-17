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
        Schema::create('nft_van_bangs', function (Blueprint $table) {
            $table->id();
            $table->morphs('nftable'); // nftable_type và nftable_id
            $table->foreignId('smart_contract_id')->constrained('smart_contracts')->onDelete('cascade');
            $table->foreignId('nhan_vien_ky_id')->nullable()->constrained('nhan_viens')->onDelete('set null');
            $table->string('hash_du_lieu')->nullable();
            $table->string('token_id')->unique()->nullable();
            $table->string('token_uri')->nullable();
            $table->string('tx_hash_thanh_cong')->nullable();
            $table->integer('trang_thai')->default(0);
            $table->text('chu_ky_so')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nft_van_bangs');
    }
};
