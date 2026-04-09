<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViNhanVien extends Model
{
    use HasFactory;

    protected $table = 'vi_nhan_viens';

    protected $fillable = [
        'nhan_vien_id',
        'dia_chi_vi',
        'trang_thai',
    ];

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class);
    }
}
