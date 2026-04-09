<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViSinhVien extends Model
{
    use HasFactory;

    protected $table = 'vi_sinh_viens';

    protected $fillable = [
        'sinh_vien_id',
        'dia_chi_vi',
        'trang_thai',
    ];

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class);
    }
}
