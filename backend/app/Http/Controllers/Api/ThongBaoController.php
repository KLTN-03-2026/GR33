<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
    /**
     * Lấy danh sách thông báo cho Admin
     */
    public function getListAdmin()
    {
        // Lấy thông báo dành riêng cho Admin này hoặc thông báo chung cho bộ phận Admin (nhan_vien_id is null)
        $danh_sach = ThongBao::where(function($q) {
                $q->where('nhan_vien_id', Auth::guard('sanctum')->id())
                  ->orWhereNull('nhan_vien_id');
            })
            ->whereNull('sinh_vien_id')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $danh_sach,
            'chua_doc' => $danh_sach->where('is_read', false)->count()
        ]);
    }

    /**
     * Lấy danh sách thông báo cho Sinh viên
     */
    public function getListSinhVien()
    {
        $danh_sach = ThongBao::where('sinh_vien_id', Auth::guard('sanctum')->id())
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $danh_sach,
            'chua_doc' => $danh_sach->where('is_read', false)->count()
        ]);
    }

    /**
     * Đánh dấu đã đọc một thông báo
     */
    public function markAsRead(Request $request)
    {
        $id = $request->id;
        $thong_bao = ThongBao::find($id);

        if ($thong_bao) {
            $thong_bao->is_read = true;
            $thong_bao->save();
            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false, 'message' => 'Không tìm thấy thông báo!']);
    }

    /**
     * Đánh dấu tất cả là đã đọc (Admin)
     */
    public function readAllAdmin()
    {
        ThongBao::where(function($q) {
                $q->where('nhan_vien_id', Auth::guard('sanctum')->id())
                  ->orWhereNull('nhan_vien_id');
            })
            ->whereNull('sinh_vien_id')
            ->update(['is_read' => true]);

        return response()->json(['status' => true, 'message' => 'Đã đánh dấu tất cả thông báo là đã đọc.']);
    }

    /**
     * Đánh dấu tất cả là đã đọc (Sinh viên)
     */
    public function readAllSinhVien()
    {
        ThongBao::where('sinh_vien_id', Auth::guard('sanctum')->id())
            ->update(['is_read' => true]);

        return response()->json(['status' => true, 'message' => 'Đã đánh dấu tất cả thông báo là đã đọc.']);
    }
}
