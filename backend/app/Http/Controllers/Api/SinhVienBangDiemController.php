<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BangDiem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SinhVienBangDiemController extends Controller
{
    /**
     * Sinh viên lấy danh sách bảng điểm của chính mình
     */
    public function getMyBangDiem()
    {
        $id_sinh_vien = Auth::guard('sanctum')->id();
        $danh_sach = BangDiem::with(['lopHoc.monHoc', 'nftVanBang.lichSuGiaoDichs' => function($q) {
            $q->where('hanh_dong', 'THU_HOI_NFT_BURN');
        }])->where('sinh_vien_id', $id_sinh_vien)->get();

        // Thêm tx_hash_burn vào nftVanBang để Frontend dễ sử dụng
        $danh_sach->each(function($item) {
            if ($item->nftVanBang && $item->nftVanBang->trang_thai === 4) {
                $lichSu = $item->nftVanBang->lichSuGiaoDichs->first();
                $item->nftVanBang->tx_hash_burn = $lichSu ? $lichSu->transaction_hash : null;
            }
        });

        return response()->json([
            'status' => true,
            'data'   => $danh_sach
        ]);
    }
}
