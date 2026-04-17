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
        // 1. Cập nhật các bảng hiện có
        Schema::table('du_ans', function (Blueprint $table) {
            $table->tinyInteger('is_phe_duyet')->default(1)->comment('0: Cho, 1: Duyet, 2: Tu choi');
            $table->text('ghi_chu_tu_choi')->nullable();
        });

        Schema::table('chung_chis', function (Blueprint $table) {
            $table->tinyInteger('is_phe_duyet')->default(1)->comment('0: Cho, 1: Duyet, 2: Tu choi');
            $table->text('ghi_chu_tu_choi')->nullable();
        });

        Schema::table('bang_diems', function (Blueprint $table) {
            $table->text('ghi_chu_tu_choi')->nullable();
        });

        // 2. Tạo bảng lịch sử phê duyệt
        Schema::create('lich_su_phe_duyets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->string('loai_phe_duyet')->comment('NFT hoặc DU_LIEU');
            $table->tinyInteger('trang_thai_moi');
            $table->text('ly_do')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_phe_duyets');

        if (Schema::hasColumn('du_ans', 'is_phe_duyet')) {
            Schema::table('du_ans', function (Blueprint $table) {
                $table->dropColumn(['is_phe_duyet', 'ghi_chu_tu_choi']);
            });
        }

        if (Schema::hasColumn('chung_chis', 'is_phe_duyet')) {
            Schema::table('chung_chis', function (Blueprint $table) {
                $table->dropColumn(['is_phe_duyet', 'ghi_chu_tu_choi']);
            });
        }

        if (Schema::hasColumn('bang_diems', 'ghi_chu_tu_choi')) {
            Schema::table('bang_diems', function (Blueprint $table) {
                $table->dropColumn('ghi_chu_tu_choi');
            });
        }
    }
};
