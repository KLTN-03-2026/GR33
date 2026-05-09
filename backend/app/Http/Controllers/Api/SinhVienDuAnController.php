<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DuAn;
use App\Models\ThongBao;
use App\Http\Requests\StoreDuAnRequest;
use App\Http\Requests\UpdateDuAnRequest;
use App\Http\Traits\CoMaTuDong;
use App\Http\Requests\SinhVienStoreDuAnRequest;
use App\Http\Requests\SinhVienUpdateDuAnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SinhVienDuAnController extends Controller
{
    use CoMaTuDong;

    /**
     * Sinh viên lấy danh sách dự án của chính mình
     */
    public function getMyDuAn()
    {
        $id_sinh_vien = Auth::guard('sanctum')->id();
        $danh_sach = DuAn::with(['nftVanBang.lichSuGiaoDichs' => function($q) {
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

    /**
     * Sinh viên thêm dự án mới (Cần phê duyệt)
     */
    public function createDuAn(SinhVienStoreDuAnRequest $request)
    {
        // Tự động gán ID sinh viên đang đăng nhập, mã dự án và trạng thái chờ duyệt
        $du_lieu = $request->validated();
        $du_lieu['sinh_vien_id'] = Auth::guard('sanctum')->id();
        $du_lieu['ma_du_an']     = $this->sinhMaDuAn(); // Tự động sinh mã DAxxx
        $du_lieu['is_phe_duyet'] = 0; // Chờ duyệt
        $du_lieu['trang_thai']   = 0; // Chưa đúc NFT

        $du_an = DuAn::create($du_lieu);

        // Gửi thông báo cho Admin
        ThongBao::create([
            'nhan_vien_id' => null, // Gửi cho tất cả Admin
            'tieu_de'      => 'Yêu cầu duyệt dự án mới',
            'noi_dung'     => 'Sinh viên ' . Auth::guard('sanctum')->user()->ho_ten . ' vừa gửi yêu cầu duyệt dự án: ' . $du_an->ten_du_an,
            'link'         => '/admin/phe-duyet',
            'loai'         => 'info'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Gửi yêu cầu thêm dự án thành công, vui lòng chờ Admin phê duyệt.'
        ]);
    }

    /**
     * Sinh viên cập nhật dự án của mình (Cần phê duyệt lại)
     */
    public function updateDuAn(SinhVienUpdateDuAnRequest $request, $id)
    {
        $id_sinh_vien = Auth::guard('sanctum')->id();
        $du_an = DuAn::where('id', $id)->where('sinh_vien_id', $id_sinh_vien)->first();

        if (!$du_an) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy dự án hoặc bạn không có quyền!']);
        }

        if ($du_an->trang_thai == 1) {
            return response()->json(['status' => false, 'message' => 'Dự án đã đúc NFT không thể chỉnh sửa!']);
        }

        if ($du_an->is_phe_duyet == 0) {
            return response()->json(['status' => false, 'message' => 'Dự án đang trong quá trình chờ phê duyệt, không thể chỉnh sửa!']);
        }

        $du_lieu = $request->validated();
        unset($du_lieu['ma_du_an']); // Không cho sửa mã
        
        $du_lieu['is_phe_duyet'] = 0; // Đưa về trạng thái chờ duyệt lại
        $du_lieu['ghi_chu_tu_choi'] = null; // Xóa ghi chú lỗi cũ

        $du_an->update($du_lieu);

        // Gửi thông báo cho Admin
        ThongBao::create([
            'nhan_vien_id' => null,
            'tieu_de'      => 'Cập nhật yêu cầu dự án',
            'noi_dung'     => 'Sinh viên ' . Auth::guard('sanctum')->user()->ho_ten . ' vừa cập nhật dự án: ' . $du_an->ten_du_an . '. Vui lòng duyệt lại.',
            'link'         => '/admin/phe-duyet',
            'loai'         => 'warning'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật thành công, vui lòng chờ Admin phê duyệt lại.'
        ]);
    }
}
