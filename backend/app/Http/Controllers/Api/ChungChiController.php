<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChungChi;
use App\Http\Requests\StoreChungChiRequest;
use App\Http\Requests\UpdateChungChiRequest;
use App\Http\Traits\CoMaTuDong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChungChiController extends Controller
{
    use CoMaTuDong;

    public function getDataChungChi()
    {
        $chungChis = ChungChi::with(['sinhVien', 'donViCap', 'nftVanBang'])
                            ->where('is_phe_duyet', 1)
                            ->get();
        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách chứng chỉ thành công',
            'data'    => $chungChis
        ]);
    }

    public function adminCreateChungChi(StoreChungChiRequest $request)
    {
        $data = $request->validated();
        
        // Luôn tự động sinh mã cho Admin để đảm bảo quy tắc hệ thống
        $data['ma_chung_chi'] = $this->sinhMaChungChi();

        if ($request->hasFile('file_dinh_kem')) {
            $file = $request->file('file_dinh_kem');
            $upload = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'dar/chung_chi',
                'use_filename' => true, 'resource_type' => 'auto'
            ]);
            $data['file_dinh_kem'] = $upload['secure_url'];
        }

        ChungChi::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Thêm chứng chỉ thành công'
        ]);
    }

    public function getDetailChungChi(ChungChi $chungChi)
    {
        $chungChi->load(['sinhVien', 'donViCap']);
        
        return response()->json([
            'status'  => true,
            'message' => 'Lấy thông tin chứng chỉ thành công',
            'data'    => $chungChi
        ]);
    }

    public function adminUpdateChungChi(UpdateChungChiRequest $request, ChungChi $chungChi)
    {
        if ($chungChi->is_locked) {
            return response()->json([
                'status'  => false,
                'message' => 'Hồ sơ chứng chỉ đang trong quá trình xử lý trên Blockchain. Vui lòng đợi.'
            ]);
        }

        $data = $request->validated();
        unset($data['ma_chung_chi']); // Không cho phép cập nhật mã

        if ($request->hasFile('file_dinh_kem')) {
            // Xóa file cũ nếu là file cục bộ
            if ($chungChi->file_dinh_kem && !str_starts_with($chungChi->file_dinh_kem, 'http') && file_exists(public_path($chungChi->file_dinh_kem))) {
                unlink(public_path($chungChi->file_dinh_kem));
            }

            $file = $request->file('file_dinh_kem');
            $upload = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'dar/chung_chi',
                'use_filename' => true, 'resource_type' => 'auto'
            ]);
            $data['file_dinh_kem'] = $upload['secure_url'];
        }
        $chungChi->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật chứng chỉ thành công'
        ]);
    }

    public function deleteChungChi(ChungChi $chungChi)
    {
        if ($chungChi->is_locked || $chungChi->trang_thai === $chungChi::STATUS_MINTED || $chungChi->nftVanBang()->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'Không thể xóa chứng chỉ đã được khóa hoặc đã đúc NFT'
            ]);
        }

        $chungChi->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa chứng chỉ thành công'
        ]);
    }


}
