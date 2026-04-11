<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChucNang extends Model
{
    use HasFactory;

    protected $table = 'chuc_nangs';

    protected $fillable = [
        'ten_chuc_nang',
        'group_id',
    ];

    public function group()
    {
        return $this->belongsTo(GroupChucNang::class, 'group_id');
    }

    public function chucVus()
    {
        return $this->belongsToMany(ChucVu::class, 'chuc_vu_chuc_nangs', 'chuc_nang_id', 'chuc_vu_id');
    }
}
