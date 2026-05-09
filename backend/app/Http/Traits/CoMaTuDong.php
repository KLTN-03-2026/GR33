<?php

namespace App\Http\Traits;

use App\Models\DuAn;
use App\Models\ChungChi;
use App\Models\NhanVien;
use App\Models\SinhVien;

trait CoMaTuDong
{
    /**
     * Sinh mã dự án duy nhất (DA + 9 số)
     */
    public function sinhMaDuAn()
    {
        do {
            $ma = 'DA' . mt_rand(100000000, 999999999);
        } while (DuAn::where('ma_du_an', $ma)->exists());

        return $ma;
    }

    /**
     * Sinh mã chứng chỉ duy nhất (CC + 9 số)
     */
    public function sinhMaChungChi()
    {
        do {
            $ma = 'CC' . mt_rand(100000000, 999999999);
        } while (ChungChi::where('ma_chung_chi', $ma)->exists());

        return $ma;
    }

    /**
     * Sinh mã sinh viên duy nhất (SV + 8 số)
     */
    public function sinhMaSinhVien()
    {
        do {
            $ma = 'SV' . mt_rand(10000000, 99999999);
        } while (SinhVien::where('ma_sinh_vien', $ma)->exists());

        return $ma;
    }

    /**
     * Sinh mã nhân viên duy nhất (NV + 8 số)
     */
    public function sinhMaNhanVien()
    {
        do {
            $ma = 'NV' . mt_rand(10000000, 99999999);
        } while (NhanVien::where('ma_nhan_vien', $ma)->exists());

        return $ma;
    }

    /**
     * Lấy ảnh đại diện mặc định cho sinh viên/nhân viên
     */
    public function layAnhMacDinhSinhVien()
    {
        return 'https://img.freepik.com/vector-cao-cap/anh-vector-minh-hoa-mau-sac-hinh-dai-dien-nguoi-dung-bieu-tuong-ho-so-ca-nhan-mot-nguoi-co-dac-diem-khuon-mat-phu-hop-cho-anh-dai-dien-tren-mang-xa-hoi-bieu-tuong-trinh-bao-ve-man-hinh-va-lam-mau_719432-2106.jpg?semt=ais_hybrid&w=740&q=80';
    }
}
