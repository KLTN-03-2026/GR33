<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhanQuyen;
use App\Models\ChucNang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhanQuyenController extends Controller
{
    public function getDataPhanQuyen()
    {
        $data = PhanQuyen::join('chuc_nangs', 'phan_quyens.chuc_nang_id', 'chuc_nangs.id')
            ->join('chuc_vus', 'phan_quyens.chuc_vu_id', 'chuc_vus.id')
            ->select('phan_quyens.*', 'chuc_nangs.ten_chuc_nang', 'chuc_vus.ten_chuc_vu')
            ->get();
            
        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu phân quyền thành công',
            'data' => $data,
        ]);
    }

    public function createPhanQuyen(Request $request)
    {
        PhanQuyen::create([
            'chuc_vu_id' => $request->chuc_vu_id,
            'chuc_nang_id' => $request->chuc_nang_id,
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Thêm mới phân quyền thành công',
        ]);
    }

    public function deletePhanQuyen(Request $request)
    {
        $phanQuyen = PhanQuyen::find($request->id);
        if ($phanQuyen) {
            $phanQuyen->delete();
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Xoá phân quyền thành công',
        ]);
    }

    public function updatePhanQuyen(Request $request)
    {
        $phanQuyen = PhanQuyen::find($request->id);
        if ($phanQuyen) {
            $phanQuyen->update([
                'chuc_vu_id' => $request->chuc_vu_id,
                'chuc_nang_id' => $request->chuc_nang_id,
            ]);
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật phân quyền thành công',
        ]);
    }
}
