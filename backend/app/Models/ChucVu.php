<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChucVu extends Model
{
    use HasFactory;

    protected $table = 'chuc_vus';

    protected $fillable = [
        'ten_chuc_vu',
        'trang_thai',
    ];

    public function chucNangs()
    {
        return $this->belongsToMany(ChucNang::class, 'chuc_vu_chuc_nangs', 'chuc_vu_id', 'chuc_nang_id');
    }

    public function nhanViens()
    {
        return $this->hasMany(NhanVien::class, 'chuc_vu_id');
    }
}
