<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\HoSoBiKhoa;

class ChungChi extends Model
{
    use HasFactory, HoSoBiKhoa;
    
    

    const STATUS_NOT_MINTED = 0;
    const STATUS_MINTED = 1;
    const STATUS_PENDING_MINT = 2;

    protected $table = 'chung_chis';

    protected $fillable = [
        'ma_chung_chi',     // Số hiệu chứng chỉ (VD: VSTEP-2026-001)
        'ten_chung_chi',    // Tên chứng chỉ (VD: Chứng chỉ Tiếng Anh B2)
        'sinh_vien_id',     // FK trỏ về sinh viên
        
        // XỬ LÝ ĐƠN VỊ CẤP (1 TRONG 2 TRƯỜNG NÀY PHẢI CÓ DỮ LIỆU)
        'don_vi_cap_id',         // Nếu chọn từ danh sách có sẵn (Ví dụ: DTU_LTC)
        'ten_don_vi_cap_khac',   // Nếu nộp chứng chỉ bên ngoài (Ví dụ: "Hội đồng Anh - British Council")
        
        'loai_chung_chi',
        'ngay_cap',
        'ngay_het_han',
        'diem_so',
        'xep_loai',
        'file_dinh_kem', // Nhớ thêm trường này như mình vừa góp ý nhé
        'trang_thai',
        'is_locked',
        'is_phe_duyet',
        'ghi_chu_tu_choi',
    ];

    protected function casts(): array
    {
        return [
            'is_phe_duyet' => 'integer',
        ];
    }

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    public function donViCap()
    {
        return $this->belongsTo(DonViCap::class, 'don_vi_cap_id');
    }

    public function nftVanBang()
    {
        return $this->morphOne(NftVanBang::class, 'nftable');
    }

    /**
     * Tự động biến file_dinh_kem thành Full URL nếu là đường dẫn nội bộ
     */
    protected function fileDinhKem(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value && !str_starts_with($value, 'http')) ? asset($value) : $value,
        );
    }
}
