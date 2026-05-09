<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BangDiem;
use App\Http\Requests\StoreBangDiemRequest;
use App\Http\Requests\UpdateBangDiemRequest;
use Illuminate\Http\Request;

class BangDiemController extends Controller
{
    public function getDataBangDiem()
    {
        $user = auth()->user();
        $query = BangDiem::with(['sinhVien', 'lopHoc.monHoc', 'nftVanBang']);

        // Nếu là Giảng viên (ID = 6), chỉ hiện bảng điểm thuộc lớp mình dạy
        if ($user && $user->chuc_vu_id == 6) {
            $query->whereHas('lopHoc', function($q) use ($user) {
                $q->where('giang_vien_id', $user->id);
            });
        }

        $bangDiems = $query->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách bảng điểm thành công',
            'data'    => $bangDiems
        ]);
    }

    public function createBangDiem(StoreBangDiemRequest $request)
    {
        $user = auth()->user();
        $lopHoc = \App\Models\LopHoc::find($request->lop_hoc_id);
        
        if ($lopHoc) {
            if ($lopHoc->trang_thai === 'dang_mo') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Lớp học đang diễn ra. Đã chốt danh sách, không thể thêm sinh viên vào lớp.'
                ]);
            }
            if ($lopHoc->trang_thai === 'da_ket_thuc') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Lớp học đã kết thúc, không thể thêm sinh viên.'
                ]);
            }
        }

        if ($user && $user->chuc_vu_id == 6 && $lopHoc) {
            if ($lopHoc->giang_vien_id != $user->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Bạn chỉ được nhập điểm cho sinh viên trong lớp mình giảng dạy.'
                ]);
            }
        }

        if ($lopHoc && $lopHoc->si_so >= 40) {
            return response()->json([
                'status'  => false,
                'message' => 'Lớp đã đủ người'
            ]);
        }

        $exists = BangDiem::where('sinh_vien_id', $request->sinh_vien_id)
            ->where('lop_hoc_id', $request->lop_hoc_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => false,
                'message' => 'Sinh viên này đã có tên trong danh sách lớp học này rồi.'
            ]);
        }

        BangDiem::create($request->validated());
        
        if ($lopHoc) {
            $lopHoc->increment('si_so');
        }

        return response()->json([
            'status'  => true,
            'message' => 'Lưu điểm và thêm sinh viên vào lớp thành công.'
        ]);
    }

    public function getDetailBangDiem(BangDiem $bangDiem)
    {
        $user = auth()->user();
        $bangDiem->load(['sinhVien', 'lopHoc.monHoc']);

        if ($user && $user->chuc_vu_id == 6 && $bangDiem->lopHoc->giang_vien_id != $user->id) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn chỉ được xem điểm của sinh viên trong lớp mình giảng dạy.'
            ]);
        }
        
        return response()->json([
            'status'  => true,
            'message' => 'Lấy thông tin bảng điểm thành công',
            'data'    => $bangDiem
        ]);
    }

    public function updateBangDiem(UpdateBangDiemRequest $request, BangDiem $bangDiem)
    {
        $user = auth()->user();

        if ($user && $user->chuc_vu_id == 6) {
            if ($bangDiem->lopHoc->giang_vien_id != $user->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Bạn chỉ được sửa điểm của sinh viên trong lớp mình giảng dạy.'
                ]);
            }

            if ($bangDiem->lopHoc->trang_thai !== 'dang_mo') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Chỉ có thể cập nhật điểm khi lớp học đang diễn ra.'
                ]);
            }
        }

        if ($bangDiem->is_locked) {
            return response()->json([
                'status'  => false,
                'message' => 'Hồ sơ đang trong quá trình xử lý trên Blockchain. Vui lòng đợi trong giây lát.'
            ]);
        }

        $bangDiem->update($request->validated());
        
        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật bảng điểm thành công'
        ]);
    }

    public function deleteBangDiem(BangDiem $bangDiem)
    {
        if ($bangDiem->is_locked || $bangDiem->trang_thai === $bangDiem::STATUS_MINTED || $bangDiem->nftVanBang()->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'Không thể xóa bảng điểm đã được khóa hoặc đã đúc NFT'
            ]);
        }

        $lopHoc = $bangDiem->lopHoc; // Quan hệ đã được load hoặc tự động nạp
        if ($lopHoc) {
            if ($lopHoc->trang_thai === 'dang_mo') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Sinh viên đang học tại lớp này. Không thể xóa bảng điểm khi lớp học đang diễn ra.'
                ]);
            }
            if ($lopHoc->trang_thai === 'da_ket_thuc') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Lớp học đã kết thúc. Dữ liệu điểm số đã được chốt và không thể xóa.'
                ]);
            }
        }

        $lopHocId = $bangDiem->lop_hoc_id;
        $bangDiem->delete();
        
        $lopHoc = \App\Models\LopHoc::find($lopHocId);
        if ($lopHoc && $lopHoc->si_so > 0) {
            $lopHoc->decrement('si_so');
        }

        return response()->json([
            'status'  => true,
            'message' => 'Xóa bảng điểm thành công'
        ]);
    }


}
