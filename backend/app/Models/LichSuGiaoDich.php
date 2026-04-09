<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSuGiaoDich extends Model
{
    use HasFactory;

    protected $table = 'lich_su_giao_dichs';

    protected $fillable = [
        'nft_van_bang_id',
        'nguoi_thuc_hien_id',
        'hanh_dong',
        'gas_used',
        'transaction_hash',
        'block_number',
        'gas_price',
        'trang_thai',
        'loi_chi_tiet',
    ];

    public function nftVanBang()
    {
        return $this->belongsTo(NftVanBang::class, 'nft_van_bang_id');
    }

    public function nguoiThucHien()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_thuc_hien_id');
    }
}
