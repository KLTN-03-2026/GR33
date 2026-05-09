<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SinhVienMonHocController extends Controller
{
    public function getData()
    {
        $sinhVienId = Auth::guard('sinh_vien')->id();

        $monHocs = MonHoc::with(['lopHocs.bangDiems' => function ($query) use ($sinhVienId) {
            $query->where('sinh_vien_id', $sinhVienId);
        }])->get();

        $data = $monHocs->map(function ($monHoc) {
            // Lấy tất cả bản ghi bảng điểm của SV này cho môn học này (thông qua các lớp học)
            $allBangDiems = $monHoc->lopHocs->flatMap->bangDiems;

            $status = 0; // Mặc định: Chưa học
            
            if ($allBangDiems->isNotEmpty()) {
                // Kiểm tra xem có bản ghi nào đã có điểm tổng kết chưa
                $daCoDiem = $allBangDiems->contains(function ($bd) {
                    return $bd->diem_tong_ket !== null;
                });

                if ($daCoDiem) {
                    $status = 2; // Đã học
                } else {
                    $status = 1; // Đang học
                }
            }

            return [
                'id'           => $monHoc->id,
                'ma_mon_hoc'   => $monHoc->ma_mon_hoc,
                'ten_mon_hoc'  => $monHoc->ten_mon_hoc,
                'so_tin_chi'   => $monHoc->so_tin_chi,
                'mo_ta'        => $monHoc->mo_ta,
                'trang_thai_hoc' => $status
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $data
        ]);
    }
}
