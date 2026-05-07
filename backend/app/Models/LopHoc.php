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

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($model) {
            // Khi trạng thái lớp học chuyển sang "Đã kết thúc"
            if ($model->wasChanged('trang_thai') && $model->trang_thai === 'da_ket_thuc') {
                // Lặp qua tất cả bảng điểm của lớp này và trigger save() 
                // để Model BangDiem tự động tính điểm tổng kết dựa trên logic ở BangDiem::boot()
                foreach ($model->bangDiems as $bd) {
                    $bd->save();
                }
            }
        });
    }
}
