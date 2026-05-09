<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChungChi;
use App\Models\ThongBao;
use App\Http\Traits\CoMaTuDong;
use App\Http\Requests\SinhVienStoreChungChiRequest;
use App\Http\Requests\SinhVienUpdateChungChiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SinhVienChungChiController extends Controller
{
    use CoMaTuDong;

    /**
     * Sinh viên lấy danh sách chứng chỉ của chính mình
     */
    public function getMyChungChi()
    {
        $id_sinh_vien = Auth::guard('sanctum')->id();
        $danh_sach = ChungChi::with(['donViCap', 'nftVanBang.lichSuGiaoDichs' => function($q) {
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
     * Sinh viên thêm chứng chỉ mới (Cần phê duyệt)
     */
    public function createChungChi(SinhVienStoreChungChiRequest $request)
    {
        $du_lieu = $request->validated();
        $du_lieu['sinh_vien_id'] = Auth::guard('sanctum')->id();
        $du_lieu['is_phe_duyet'] = 0; // Chờ duyệt
        $du_lieu['trang_thai']   = 0; // Chưa đúc NFT
        // Không tự động sinh mã, sử dụng mã do sinh viên nhập từ chứng chỉ thực tế

        // Xử lý upload file nếu có
        if ($request->hasFile('file_dinh_kem')) {
            $file = $request->file('file_dinh_kem');
            $upload = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'dar/chung_chi',
                'use_filename' => true, 'resource_type' => 'auto'
            ]);
            $du_lieu['file_dinh_kem'] = $upload['secure_url'];
        }

        $cc = ChungChi::create($du_lieu);

        // Gửi thông báo cho Admin
        ThongBao::create([
            'nhan_vien_id' => null,
            'tieu_de'      => 'Yêu cầu duyệt chứng chỉ mới',
            'noi_dung'     => 'Sinh viên ' . Auth::guard('sanctum')->user()->ho_ten . ' vừa gửi yêu cầu duyệt chứng chỉ: ' . ($cc->ten_chung_chi ?? 'N/A'),
            'link'         => '/admin/phe-duyet',
            'loai'         => 'info'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Gửi yêu cầu thêm chứng chỉ thành công, vui lòng chờ Admin phê duyệt.'
        ]);
    }

    /**
     * Sinh viên cập nhật chứng chỉ (Cần phê duyệt lại)
     */
    public function updateChungChi(SinhVienUpdateChungChiRequest $request, $id)
    {
        $id_sinh_vien = Auth::guard('sanctum')->id();
        $chung_chi = ChungChi::where('id', $id)->where('sinh_vien_id', $id_sinh_vien)->first();

        if (!$chung_chi) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy chứng chỉ hoặc bạn không có quyền!']);
        }

        if ($chung_chi->trang_thai == 1) {
            return response()->json(['status' => false, 'message' => 'Chứng chỉ đã đúc NFT không thể chỉnh sửa!']);
        }

        if ($chung_chi->is_phe_duyet == 0) {
            return response()->json(['status' => false, 'message' => 'Chứng chỉ đang trong quá trình chờ phê duyệt, không thể chỉnh sửa!']);
        }

        $du_lieu = $request->validated();
        unset($du_lieu['ma_chung_chi']); // Không cho sửa mã
        
        $du_lieu['is_phe_duyet'] = 0; // Đưa về trạng thái chờ duyệt lại
        $du_lieu['ghi_chu_tu_choi'] = null; // Xóa ghi chú cũ

        // Xử lý upload file mới
        if ($request->hasFile('file_dinh_kem')) {
            $file = $request->file('file_dinh_kem');
            $upload = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'dar/chung_chi',
                'use_filename' => true, 'resource_type' => 'auto'
            ]);
            $du_lieu['file_dinh_kem'] = $upload['secure_url'];
        }

        $chung_chi->update($du_lieu);

        // Gửi thông báo cho Admin
        ThongBao::create([
            'nhan_vien_id' => null,
            'tieu_de'      => 'Cập nhật yêu cầu chứng chỉ',
            'noi_dung'     => 'Sinh viên ' . Auth::guard('sanctum')->user()->ho_ten . ' vừa cập nhật chứng chỉ: ' . ($chung_chi->ten_chung_chi ?? 'N/A') . '. Vui lòng duyệt lại.',
            'link'         => '/admin/phe-duyet',
            'loai'         => 'warning'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật thành công, vui lòng chờ Admin phê duyệt lại.'
        ]);
    }
}
