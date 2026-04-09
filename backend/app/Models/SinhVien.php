<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SinhVien extends Authenticatable
{
    use HasFactory, HasApiTokens;
    
    protected $table = 'sinh_viens';

    protected $fillable = [
        'ma_sinh_vien',
        'ho_ten',
        'nganh_hoc',
        'mat_khau',
        'email',
        'nam_bat_dau',
        'so_nam_hoc',
        'so_dien_thoai',
        'dia_chi',
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

    public function viSinhVien()
    {
        return $this->hasOne(ViSinhVien::class, 'sinh_vien_id');
    }

    public function bangDiems()
    {
        return $this->hasMany(BangDiem::class, 'sinh_vien_id');
    }

    public function chungChis()
    {
        return $this->hasMany(ChungChi::class, 'sinh_vien_id');
    }

    public function duAns()
    {
        return $this->hasMany(DuAn::class, 'sinh_vien_id');
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
