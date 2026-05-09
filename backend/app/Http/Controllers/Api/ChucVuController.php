<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChucVu;
use App\Http\Requests\StoreChucVuRequest;
use App\Http\Requests\UpdateChucVuRequest;
use Illuminate\Http\Request;

class ChucVuController extends Controller
{
    public function getDataChucVu()
    {
        $chucVus = ChucVu::all();
        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách chức vụ thành công',
            'data'    => $chucVus
        ]);
    }

    public function createChucVu(StoreChucVuRequest $request)
    {
        ChucVu::create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Thêm chức vụ thành công'
        ]);
    }

    public function updateChucVu(UpdateChucVuRequest $request, ChucVu $chucVu)
    {
        $chucVu->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật chức vụ thành công'
        ]);
    }

    public function deleteChucVu(ChucVu $chucVu)
    {
        if ($chucVu->nhanViens()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa chức vụ này vì đã có nhân viên tham chiếu'
            ]);
        }

        $chucVu->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa chức vụ thành công'
        ]);
    }

    public function getDataChucVuAllowed()
    {
        $data = ChucVu::where('id', '!=', 1)->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function changeStatusChucVu(Request $request)
    {
        $chucVu = ChucVu::find($request->id);
        if ($chucVu) {
            $chucVu->trang_thai = $request->trang_thai;
            $chucVu->save();
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Đã cập nhật trạng thái chức vụ thành công',
        ]);
    }

    public function searchChucVu(Request $request)
    {
        $keyword = $request->keyword;
        $data = ChucVu::where('ten_chuc_vu', 'like', '%' . $keyword . '%')->get();

        return response()->json([
            'status' => true,
            'message' => 'Tìm kiếm chức vụ thành công',
            'data' => $data
        ]);
    }
}
