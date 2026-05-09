<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DuAn;
use App\Http\Requests\StoreDuAnRequest;
use App\Http\Requests\UpdateDuAnRequest;
use App\Http\Traits\CoMaTuDong;
use Illuminate\Http\Request;

class DuAnController extends Controller
{
    use CoMaTuDong;

    public function getDataDuAn()
    {
        $duAns = DuAn::with(['sinhVien', 'nftVanBang'])
                    ->where('is_phe_duyet', 1)
                    ->get();
        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách dự án thành công',
            'data'    => $duAns
        ]);
    }

    public function adminCreateDuAn(StoreDuAnRequest $request)
    {
        $du_lieu = $request->validated();
        $du_lieu['ma_du_an'] = $this->sinhMaDuAn(); // Tự động sinh mã

        DuAn::create($du_lieu);

        return response()->json([
            'status'  => true,
            'message' => 'Thêm dự án thành công'
        ]);
    }

    public function getDetailDuAn(DuAn $duAn)
    {
        $duAn->load(['sinhVien']);
        
        return response()->json([
            'status'  => true,
            'message' => 'Lấy thông tin dự án thành công',
            'data'    => $duAn
        ]);
    }

    public function adminUpdateDuAn(UpdateDuAnRequest $request, DuAn $duAn)
    {
        if ($duAn->is_locked) {
            return response()->json([
                'status'  => false,
                'message' => 'Hồ sơ dự án đang trong quá trình xử lý trên Blockchain. Vui lòng đợi.'
            ]);
        }

        $du_lieu = $request->validated();
        unset($du_lieu['ma_du_an']); // Không cho phép cập nhật mã
        
        $duAn->update($du_lieu);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật dự án thành công'
        ]);
    }

    public function deleteDuAn(DuAn $duAn)
    {
        if ($duAn->is_locked || $duAn->trang_thai === $duAn::STATUS_MINTED || $duAn->nftVanBang()->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'Không thể xóa dự án đã được khóa hoặc đã đúc NFT'
            ]);
        }

        $duAn->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa dự án thành công'
        ]);
    }


}
