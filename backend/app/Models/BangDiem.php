<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SinhVien;
use App\Models\LopHoc;
use Illuminate\Support\Facades\Log;
use App\Traits\HoSoBiKhoa;

class BangDiem extends Model
{
    use HasFactory, HoSoBiKhoa;

    const STATUS_NOT_MINTED = 0;
    const STATUS_MINTED = 1;
    const STATUS_PENDING_MINT = 2;

    protected $fillable = [
        'sinh_vien_id',
        'lop_hoc_id',
        'diem_qua_trinh',
        'diem_cuoi_ky',
        'diem_tong_ket',
        'diem_he_4',
        'diem_chu',
        'ngay_vao_diem',
        'trang_thai',
        'is_locked',
        'ghi_chu_tu_choi',
    ];


    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    public function lopHoc()
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $lopHoc = $model->lopHoc ?: LopHoc::find($model->lop_hoc_id);
            
            if ($lopHoc && $lopHoc->trang_thai === 'da_ket_thuc') {
                if ($model->diem_qua_trinh !== null && $model->diem_cuoi_ky !== null) {
                    $model->diem_tong_ket = round(($model->diem_qua_trinh * 0.45) + ($model->diem_cuoi_ky * 0.55), 1);
                    
                    $grades = self::convertScore10to4($model->diem_tong_ket);
                    $model->diem_chu = $grades['chu'];
                    $model->diem_he_4 = $grades['he_4'];
                    $model->ngay_vao_diem = date('Y-m-d');
                }
            } elseif ($lopHoc && $lopHoc->trang_thai === 'dang_mo') {
                $model->diem_tong_ket = null;
                $model->diem_chu = null;
                $model->diem_he_4 = null;
                $model->trang_thai = self::STATUS_NOT_MINTED;
            }
        });
    }

    public static function convertScore10to4($score10)
    {
        if ($score10 >= 9.5) return ['chu' => 'A+', 'he_4' => 4.0];
        if ($score10 >= 8.5) return ['chu' => 'A', 'he_4' => 4.0];
        if ($score10 >= 8.0) return ['chu' => 'A-', 'he_4' => 3.65];
        if ($score10 >= 7.5) return ['chu' => 'B+', 'he_4' => 3.33];
        if ($score10 >= 7.0) return ['chu' => 'B', 'he_4' => 3.0];
        if ($score10 >= 6.5) return ['chu' => 'B-', 'he_4' => 2.65];
        if ($score10 >= 6.0) return ['chu' => 'C+', 'he_4' => 2.33];
        if ($score10 >= 5.5) return ['chu' => 'C', 'he_4' => 2.0];
        if ($score10 >= 4.5) return ['chu' => 'C-', 'he_4' => 1.65];
        if ($score10 >= 4.0) return ['chu' => 'D', 'he_4' => 1.0];
        return ['chu' => 'F', 'he_4' => 0.0];
    }

    public function nftVanBang()
    {
        return $this->morphOne(NftVanBang::class, 'nftable');
    }
}
