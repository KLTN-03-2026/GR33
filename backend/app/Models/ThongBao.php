<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    protected $table = 'thong_baos';

    protected $fillable = [
        'nhan_vien_id',
        'sinh_vien_id',
        'tieu_de',
        'noi_dung',
        'link',
        'is_read',
        'loai',
    ];

    /**
     * Thông báo thuộc về một Nhân viên (Admin)
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    /**
     * Thông báo thuộc về một Sinh viên
     */
    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }
}
