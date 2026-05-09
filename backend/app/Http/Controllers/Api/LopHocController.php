<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Http\Requests\StoreLopHocRequest;
use App\Http\Requests\UpdateLopHocRequest;
use Illuminate\Http\Request;

class LopHocController extends Controller
{
    public function getDataLopHoc()
    {
        $user = auth()->user();
        
        $query = LopHoc::select('id', 'ma_lop_hoc', 'ten_lop_hoc', 'mon_hoc_id', 'giang_vien_id', 'nam_hoc', 'hoc_ky', 'trang_thai', 'si_so')
                       ->with([
                           'monHoc:id,ma_mon_hoc,ten_mon_hoc,so_tin_chi', 
                           'giangVien:id,ma_nhan_vien,ho_ten,email'
                       ]);

        // Nếu là Giảng viên (ID = 6), chỉ hiện lớp đang dạy
        if ($user && $user->chuc_vu_id == 6) {
            $query->where('giang_vien_id', $user->id);
        }

        $lopHocs = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách lớp học thành công',
            'data'    => $lopHocs
        ]);
    }

    public function createLopHoc(StoreLopHocRequest $request)
    {
        LopHoc::create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Thêm lớp học thành công'
        ]);
    }

    public function getDetailLopHoc(LopHoc $lopHoc)
    {
        $user = auth()->user();

        // Nếu là Giảng viên, chỉ cho xem lớp mình dạy
        if ($user && $user->chuc_vu_id == 6 && $lopHoc->giang_vien_id != $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không có quyền xem thông tin lớp học này'
            ]);
        }

        $lopHoc->load(['monHoc', 'giangVien']);
        
        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin lớp học thành công',
            'data'    => $lopHoc
        ]);
    }

    public function updateLopHoc(UpdateLopHocRequest $request, LopHoc $lopHoc)
    {
        $lopHoc->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật lớp học thành công'
        ]);
    }

    public function deleteLopHoc(LopHoc $lopHoc)
    {
        if ($lopHoc->bangDiems()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa lớp học này vì đã có danh sách sinh viên/điểm thuộc lớp này'
            ]);
        }

        $lopHoc->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa lớp học thành công'
        ]);
    }


}
