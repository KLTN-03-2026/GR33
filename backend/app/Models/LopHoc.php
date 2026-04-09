<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LopHoc extends Model
{
    use HasFactory;

    protected $table = 'lop_hocs';

    protected $fillable = [
        'ma_lop_hoc',
        'ten_lop_hoc',
        'mon_hoc_id',
        'giang_vien_id',
        'nam_hoc',
        'hoc_ky',
        'trang_thai',
        'si_so',
    ];

    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    public function giangVien()
    {
        return $this->belongsTo(NhanVien::class, 'giang_vien_id');
    }

    public function bangDiems()
    {
        return $this->hasMany(BangDiem::class, 'lop_hoc_id');
    }
}
