<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\BangDiem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SinhVienLopHocController extends Controller
{
    public function getData(Request $request)
    {
        $sinhVienId = Auth::guard('sinh_vien')->id();

        $query = LopHoc::with(['monHoc', 'giangVien']);

        // Bộ lọc
        if ($request->has('mon_hoc_id') && $request->mon_hoc_id) {
            $query->where('mon_hoc_id', $request->mon_hoc_id);
        }

        if ($request->has('nam_hoc') && $request->nam_hoc) {
            $query->where('nam_hoc', 'like', '%' . $request->nam_hoc . '%');
        }

        if ($request->has('trang_thai') && $request->trang_thai) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $lopHocs = $query->get();
        $sinhVien = Auth::guard('sinh_vien')->user();

        // Gắn thêm thông tin đã đăng ký chưa
        $daDangKyIds = BangDiem::where('sinh_vien_id', $sinhVienId)
            ->pluck('lop_hoc_id')
            ->toArray();

        foreach ($lopHocs as $lopHoc) {
            $lopHoc->da_dang_ky = in_array($lopHoc->id, $daDangKyIds);
        }

        return response()->json([
            'status' => true,
            'data'   => $lopHocs,
            'trang_thai_sv' => $sinhVien->trang_thai
        ]);
    }

    public function dangKy(Request $request)
    {
        $request->validate([
            'lop_hoc_id' => 'required|exists:lop_hocs,id'
        ]);

        $sinhVien = Auth::guard('sinh_vien')->user();
        $sinhVienId = $sinhVien->id;

        // Kiểm tra trạng thái sinh viên
        if ($sinhVien->trang_thai !== 1) {
            $statusText = 'không xác định';
            switch ($sinhVien->trang_thai) {
                case 0: $statusText = 'đã nghỉ học'; break;
                case 2: $statusText = 'đang bảo lưu'; break;
                case 3: $statusText = 'đã tốt nghiệp'; break;
            }
            return response()->json([
                'status'  => false,
                'message' => 'Bạn hiện đang ' . $statusText . ', không thể đăng ký lớp học mới'
            ]);
        }

        $lopHoc = LopHoc::findOrFail($request->lop_hoc_id);

        // Chỉ cho phép đăng ký lớp sắp bắt đầu
        if ($lopHoc->trang_thai !== 'sap_bat_dau') {
            return response()->json([
                'status'  => false,
                'message' => 'Lớp học này hiện không trong giai đoạn đăng ký'
            ]);
        }

        // Kiểm tra đã đăng ký chưa
        $exists = BangDiem::where('sinh_vien_id', $sinhVienId)
            ->where('lop_hoc_id', $request->lop_hoc_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn đã đăng ký lớp học này rồi'
            ]);
        }
        
        // Kiểm tra xem đã học môn này chưa (nếu đã học xong thì không cho đăng ký lại?)
        // Tùy theo yêu cầu, ở đây tôi cho phép đăng ký nếu chưa có trong danh sách lớp này.

        BangDiem::create([
            'sinh_vien_id' => $sinhVienId,
            'lop_hoc_id'   => $request->lop_hoc_id,
            'trang_thai'   => BangDiem::STATUS_NOT_MINTED // Mặc định chưa đúc NFT
        ]);

        // Cập nhật sĩ số (tùy chọn)
        $lopHoc->increment('si_so');

        return response()->json([
            'status'  => true,
            'message' => 'Đăng ký lớp học thành công'
        ]);
    }

    public function huyDangKy(Request $request)
    {
        $request->validate([
            'lop_hoc_id' => 'required|exists:lop_hocs,id'
        ]);

        $sinhVienId = Auth::guard('sinh_vien')->id();
        $lopHoc = LopHoc::findOrFail($request->lop_hoc_id);

        if ($lopHoc->trang_thai !== 'sap_bat_dau') {
            return response()->json([
                'status'  => false,
                'message' => 'Lớp học đã chốt danh sách hoặc đã kết thúc, không thể hủy đăng ký'
            ]);
        }

        $bangDiem = BangDiem::where('sinh_vien_id', $sinhVienId)
            ->where('lop_hoc_id', $request->lop_hoc_id)
            ->first();

        if (!$bangDiem) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn chưa đăng ký lớp học này'
            ]);
        }

        $bangDiem->delete();
        $lopHoc->decrement('si_so');

        return response()->json([
            'status'  => true,
            'message' => 'Hủy đăng ký lớp học thành công'
        ]);
    }
}
