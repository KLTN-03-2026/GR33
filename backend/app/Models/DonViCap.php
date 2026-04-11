<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonViCap extends Model
{
    use HasFactory;

    protected $table = 'don_vi_caps';

    protected $fillable = [
        'ma_don_vi',
        'ten_don_vi',
        'loai_don_vi',
    ];

    public function chungChis()
    {
        return $this->hasMany(ChungChi::class, 'don_vi_cap_id');
    }
}
