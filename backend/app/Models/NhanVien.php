<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class NhanVien extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'nhan_viens';

    protected $fillable = [
        'chuc_vu_id',
        'ma_nhan_vien',
        'ho_ten',
        'email',
        'mat_khau',
        'so_dien_thoai',
        'dia_chi',
        'phong_ban_id',
        'trang_thai',
        'hinh_anh',
    ];

    protected $hidden = [
        'mat_khau',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    public function chucVu()
    {
        return $this->belongsTo(ChucVu::class, 'chuc_vu_id');
    }

    public function phongBan()
    {
        return $this->belongsTo(PhongBan::class, 'phong_ban_id');
    }

    public function viNhanVien()
    {
        return $this->hasOne(ViNhanVien::class, 'nhan_vien_id');
    }

    public function lopHocs()
    {
        return $this->hasMany(LopHoc::class, 'giang_vien_id');
    }

    /**
     * Tự động biến hinh_anh thành Full URL nếu là đường dẫn nội bộ
     */
    protected function hinhAnh(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value && !str_starts_with($value, 'http')) ? asset($value) : $value,
        );
    }
}
