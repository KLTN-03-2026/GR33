<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'ten_contract',
        'dia_chi_contract',
        'loai_contract',
        'abi',
        'trang_thai',
    ];

    public function nftVanBangs()
    {
        return $this->hasMany(NftVanBang::class);
    }
}
