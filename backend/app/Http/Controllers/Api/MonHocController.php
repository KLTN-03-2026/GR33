<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonHoc;
use App\Http\Requests\StoreMonHocRequest;
use App\Http\Requests\UpdateMonHocRequest;
use Illuminate\Http\Request;

class MonHocController extends Controller
{
    public function getDataMonHoc()
    {
        $monHocs = MonHoc::all();
        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách môn học thành công',
            'data'    => $monHocs
        ]);
    }

    public function createMonHoc(StoreMonHocRequest $request)
    {
        MonHoc::create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Thêm môn học thành công'
        ]);
    }

    public function getDetailMonHoc(MonHoc $monHoc)
    {
        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin môn học thành công',
            'data'    => $monHoc
        ]);
    }

    public function updateMonHoc(UpdateMonHocRequest $request, MonHoc $monHoc)
    {
        $monHoc->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật môn học thành công'
        ]);
    }

    public function deleteMonHoc(MonHoc $monHoc)
    {
        if ($monHoc->lopHocs()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa môn học này vì đã có lớp học được mở cho môn này'
            ]);
        }

        $monHoc->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa môn học thành công'
        ]);
    }


}
