<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhongBan;
use App\Http\Requests\StorePhongBanRequest;
use App\Http\Requests\UpdatePhongBanRequest;
use Illuminate\Http\Request;

class PhongBanController extends Controller
{
    public function getDataPhongBan()
    {
        $phongBans = PhongBan::all();
        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách phòng ban thành công',
            'data'    => $phongBans
        ]);
    }

    public function createPhongBan(StorePhongBanRequest $request)
    {
        PhongBan::create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Thêm phòng ban thành công'
        ]);
    }

    public function getDetailPhongBan(PhongBan $phongBan)
    {
        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin phòng ban thành công',
            'data'    => $phongBan
        ]);
    }

    public function updatePhongBan(UpdatePhongBanRequest $request, PhongBan $phongBan)
    {
        $phongBan->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật phòng ban thành công'
        ]);
    }

    public function deletePhongBan(PhongBan $phongBan)
    {
        if ($phongBan->nhanViens()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa phòng ban này vì đang có nhân viên thuộc phòng ban này'
            ]);
        }

        $phongBan->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa phòng ban thành công'
        ]);
    }


}
