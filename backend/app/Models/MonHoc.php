<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonHoc extends Model
{
    use HasFactory;

    protected $table = 'mon_hocs';

    protected $fillable = [
        'ma_mon_hoc',
        'ten_mon_hoc',
        'so_tin_chi',
        'mo_ta',
    ];

    public function lopHocs()
    {
        return $this->hasMany(LopHoc::class, 'mon_hoc_id');
    }
}
