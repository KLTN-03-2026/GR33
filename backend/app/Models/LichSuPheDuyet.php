<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuPheDuyet extends Model
{
    protected $table = 'lich_su_phe_duyets';

    protected $fillable = [
        'admin_id',
        'model_type',
        'model_id',
        'loai_phe_duyet',
        'trang_thai_moi',
        'ly_do',
    ];

    public function admin()
    {
        return $this->belongsTo(NhanVien::class, 'admin_id');
    }
}
