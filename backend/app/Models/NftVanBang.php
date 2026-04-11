<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NftVanBang extends Model
{
    use HasFactory;

    protected $table = 'nft_van_bangs';

    const STATUS_PENDING = 0; // Chờ ký
    const STATUS_SUCCESS = 1; // Thành công
    const STATUS_FAILURE = 2; // Thất bại
    const STATUS_MINTING = 3; // Đang đúc (Chờ xác nhận từ Blockchain)
    const STATUS_REVOKED = 4; // Đã thu hồi

    protected $fillable = [
        'nftable_type',
        'nftable_id',
        'smart_contract_id',
        'nhan_vien_ky_id',
        'hash_du_lieu',
        'token_id',
        'token_uri',
        'tx_hash_thanh_cong',
        'trang_thai',
        'chu_ky_so',
    ];

    public function nftable()
    {
        return $this->morphTo();
    }

    public function smartContract()
    {
        return $this->belongsTo(SmartContract::class);
    }

    public function nhanVienKy()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_ky_id');
    }

    public function lichSuGiaoDichs()
    {
        return $this->hasMany(LichSuGiaoDich::class, 'nft_van_bang_id');
    }
}
