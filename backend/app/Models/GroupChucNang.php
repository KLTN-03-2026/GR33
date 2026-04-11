<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupChucNang extends Model
{
    use HasFactory;

    protected $table = 'group_chuc_nangs';

    protected $fillable = [
        'ten_group',
    ];

    public function chucNangs()
    {
        return $this->hasMany(ChucNang::class, 'group_id');
    }
}
