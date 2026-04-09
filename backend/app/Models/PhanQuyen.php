<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhanQuyen extends Model
{
    use HasFactory;

    protected $table = 'phan_quyens';

    protected $fillable = [
        'chuc_vu_id',
        'chuc_nang_id',
    ];

    public function chucVu()
    {
        return $this->belongsTo(ChucVu::class, 'chuc_vu_id');
    }

    public function chucNang()
    {
        return $this->belongsTo(ChucNang::class, 'chuc_nang_id');
    }
}
