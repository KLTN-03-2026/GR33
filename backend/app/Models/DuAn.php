<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HoSoBiKhoa;

class DuAn extends Model
{
    use HasFactory, HoSoBiKhoa;

    const STATUS_NOT_MINTED = 0;
    const STATUS_MINTED = 1;
    const STATUS_PENDING_MINT = 2;

    protected $table = 'du_ans';

    protected $fillable = [
        'ma_du_an',
        'ten_du_an',
        'mo_ta',
        'sinh_vien_id',
        'link_du_an',
        'trang_thai',
        'is_locked',
        'is_phe_duyet',
        'ghi_chu_tu_choi',
    ];


    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    public function nftVanBang()
    {
        return $this->morphOne(NftVanBang::class, 'nftable');
    }
}
