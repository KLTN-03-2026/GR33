<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NhanVien;
use App\Models\ViNhanVien;
use App\Http\Requests\StoreNhanVienRequest;
use App\Http\Requests\UpdateNhanVienRequest;
use App\Http\Requests\NhanVienUpdateProfileRequest;
use App\Http\Traits\CoMaTuDong;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NhanVienController extends Controller
{
    use CoMaTuDong;

    public function getDataNhanVien()
    {
        $nhanViens = NhanVien::with(['chucVu', 'phongBan'])->get();
        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách nhân viên thành công',
            'data'    => $nhanViens
        ]);
    }

    public function createNhanVien(StoreNhanVienRequest $request)
    {
        $validated = $request->validated();
        
        // Tự động sinh mã nhân viên và gán ảnh mặc định
        $validated['ma_nhan_vien'] = $this->sinhMaNhanVien();
        if (empty($validated['hinh_anh'])) {
            $validated['hinh_anh'] = $this->layAnhMacDinhSinhVien();
        }

        $matKhau = $request->mat_khau ?: 'nhanvien123456';
        $validated['mat_khau'] = Hash::make($matKhau);

        NhanVien::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Thêm nhân viên thành công'
        ]);
    }

    public function getDetailNhanVien(NhanVien $nhanVien)
    {
        $nhanVien->load(['chucVu', 'phongBan']);
        
        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin nhân viên thành công',
            'data'    => $nhanVien
        ]);
    }

    public function updateNhanVien(UpdateNhanVienRequest $request, NhanVien $nhanVien)
    {
        $validated = $request->validated();
        unset($validated['ma_nhan_vien']); // Không cho sửa mã

        $nhanVien->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật nhân viên thành công'
        ]);
    }

    public function deleteNhanVien(NhanVien $nhanVien)
    {
        if ($nhanVien->lopHocs()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa nhân viên này vì đang phụ trách giảng dạy các lớp học'
            ]);
        }

        if ($nhanVien->viNhanVien()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa nhân viên này vì đã có tài khoản ví điện tử'
            ]);
        }

        $nhanVien->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa nhân viên thành công'
        ]);
    }

    public function changeStatusNhanVien(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:nhan_viens,id',
            'trang_thai' => 'required|integer|in:0,1,2',
        ]);

        $nhanVien = NhanVien::find($request->id);
        $nhanVien->trang_thai = $request->trang_thai;
        $nhanVien->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật trạng thái nhân viên thành công',
        ]);
    }

    /**
     * Lấy thông tin hồ sơ nhân viên hiện tại
     */
    public function getProfile()
    {
        $user = auth()->user();
        $user->load(['chucVu.chucNangs', 'phongBan', 'viNhanVien']);
        
        $userData = $user->toArray();
        $userData['list_quyens'] = $user->chucVu ? $user->chucVu->chucNangs->pluck('id')->toArray() : [];

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin tài khoản thành công',
            'data'    => $userData
        ]);
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile(NhanVienUpdateProfileRequest $request)
    {
        $user = auth()->user();
        
        $data = $request->validated();

        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $upload = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'dar/avatar_admin',
                'use_filename' => true, 
                'resource_type' => 'auto'
            ]);
            $data['hinh_anh'] = $upload['secure_url'];
        }

        $user->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thông tin cá nhân thành công',
            'data'    => $user
        ]);
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'new_password.min'       => 'Mật khẩu mới phải từ 6 ký tự.',
        ]);

        if (!Hash::check($request->old_password, $user->mat_khau)) {
            return response()->json([
                'status' => false,
                'message' => 'Mật khẩu cũ không chính xác.'
            ]);
        }

        $user->mat_khau = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Đổi mật khẩu thành công.'
        ]);
    }

    /**
     * Cập nhật ví nhân viên (Chỉ được làm 1 lần)
     */
    public function updateWallet(Request $request)
    {
        $user = auth()->user();

        // 1. Kiểm tra xem đã có ví chưa
        if ($user->viNhanVien()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn đã thiết lập ví Blockchain rồi. Không thể thay đổi địa chỉ ví để đảm bảo bảo mật.'
            ]);
        }

        $request->validate([
            'dia_chi_vi' => 'required|string|size:42|regex:/^0x[a-fA-F0-9]{40}$/',
        ], [
            'dia_chi_vi.required' => 'Vui lòng nhập địa chỉ ví.',
            'dia_chi_vi.size'     => 'Địa chỉ ví Ethereum phải có độ dài 42 ký tự.',
            'dia_chi_vi.regex'    => 'Địa chỉ ví không đúng định dạng (0x...).',
        ]);

        DB::beginTransaction();
        try {
            ViNhanVien::create([
                'nhan_vien_id' => $user->id,
                'dia_chi_vi'   => $request->dia_chi_vi,
                'trang_thai'   => 1 // Hoạt động
            ]);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Thiết lập ví Blockchain thành công. Hãy bảo mật ví này cẩn thận!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi tạo ví: ' . $e->getMessage()
            ]);
        }
    }
}
