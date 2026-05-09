<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonViCap;
use App\Http\Requests\StoreDonViCapRequest;
use App\Http\Requests\UpdateDonViCapRequest;
use Illuminate\Http\Request;

class DonViCapController extends Controller
{
    public function getDataDonViCap()
    {
        $donViCaps = DonViCap::all();
        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách đơn vị cấp thành công',
            'data'    => $donViCaps
        ]);
    }

    public function createDonViCap(StoreDonViCapRequest $request)
    {
        DonViCap::create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Thêm đơn vị cấp thành công'
        ]);
    }

    public function getDetailDonViCap(DonViCap $donViCap)
    {
        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin đơn vị cấp thành công',
            'data'    => $donViCap
        ]);
    }

    public function updateDonViCap(UpdateDonViCapRequest $request, DonViCap $donViCap)
    {
        $donViCap->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật đơn vị cấp thành công'
        ]);
    }

    public function deleteDonViCap(DonViCap $donViCap)
    {
        if ($donViCap->chungChis()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa đơn vị cấp này vì đã có chứng chỉ được cấp bởi đơn vị này'
            ]);
        }

        $donViCap->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa đơn vị cấp thành công'
        ]);
    }


}
