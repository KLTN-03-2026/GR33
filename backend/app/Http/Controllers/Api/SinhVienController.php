<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SinhVien;
use App\Models\ViSinhVien;
use App\Http\Requests\StoreSinhVienRequest;
use App\Http\Requests\UpdateSinhVienRequest;
use App\Http\Requests\SinhVienUpdateProfileRequest;
use App\Http\Traits\CoMaTuDong;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SinhVienController extends Controller
{
    use CoMaTuDong;

    public function getDataSinhVien()
    {
        $user = auth()->user();
        
        // Chỉ chọn những cột cần thiết để tiết kiệm memory
        $query = SinhVien::select('id', 'ma_sinh_vien', 'ho_ten', 'nganh_hoc', 'email', 'nam_bat_dau', 'so_nam_hoc', 'so_dien_thoai', 'dia_chi', 'trang_thai', 'hinh_anh');

        // Nếu là Giảng viên (ID = 6), chỉ hiển thị sinh viên trong các lớp mình dạy
        if ($user && $user->chuc_vu_id == 6) {
            $query->whereHas('bangDiems', function ($q) use ($user) {
                $q->whereHas('lopHoc', function ($sq) use ($user) {
                    $sq->where('giang_vien_id', $user->id);
                });
            });
        }

        $sinhViens = $query->get();
        
        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách sinh viên thành công',
            'data'    => $sinhViens
        ]);
    }

    public function createSinhVien(StoreSinhVienRequest $request)
    {
        $validated = $request->validated();
        
        // Tự động sinh mã và gán ảnh mặc định
        $validated['ma_sinh_vien'] = $this->sinhMaSinhVien();
        if (empty($validated['hinh_anh'])) {
            $validated['hinh_anh'] = $this->layAnhMacDinhSinhVien();
        }

        $matKhau = $request->mat_khau ?: 'sinhvien123';
        $validated['mat_khau'] = Hash::make($matKhau);

        SinhVien::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Thêm sinh viên thành công'
        ]);
    }

    public function getDetailSinhVien(SinhVien $sinhVien)
    {
        $user = auth()->user();

        // Nếu là Giảng viên, chỉ xem được sinh viên trong lớp mình dạy
        if ($user && $user->chuc_vu_id == 6) {
            $hasStudent = $sinhVien->bangDiems()->whereHas('lopHoc', function ($q) use ($user) {
                $q->where('giang_vien_id', $user->id);
            })->exists();

            if (!$hasStudent) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn không có quyền xem thông tin sinh viên này'
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin sinh viên thành công',
            'data'    => $sinhVien
        ]);
    }

    public function updateSinhVien(UpdateSinhVienRequest $request, SinhVien $sinhVien)
    {
        $validated = $request->validated();

        $sinhVien->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật sinh viên thành công'
        ]);
    }

    public function deleteSinhVien(SinhVien $sinhVien)
    {
        if ($sinhVien->bangDiems()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa sinh viên này vì đã có dữ liệu bảng điểm'
            ]);
        }

        if ($sinhVien->chungChis()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa sinh viên này vì đã có dữ liệu chứng chỉ'
            ]);
        }

        if ($sinhVien->duAns()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa sinh viên này vì đã có dữ liệu dự án'
            ]);
        }

        if ($sinhVien->viSinhVien()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xóa sinh viên này vì đã có tài khoản ví điện tử'
            ]);
        }

        $sinhVien->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa sinh viên thành công'
        ]);
    }

    /**
     * Lấy thông tin hồ sơ sinh viên hiện tại (kèm ví)
     */
    public function getProfile()
    {
        $user = auth()->user();

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin tài khoản thành công',
            'data'    => $user->load('viSinhVien')
        ]);
    }

    /**
     * Cập nhật thông tin cá nhân sinh viên
     */
    public function updateProfile(SinhVienUpdateProfileRequest $request)
    {
        $user = auth()->user();
        
        $data = $request->validated();

        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $upload = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'dar/avatar_sinh_vien',
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
     * Đổi mật khẩu sinh viên
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
     * Cập nhật ví sinh viên (Chỉ được làm 1 lần)
     */
    public function updateWallet(Request $request)
    {
        $user = auth()->user();

        if ($user->viSinhVien()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn đã thiết lập ví Blockchain rồi. Không thể thay đổi để đảm bảo bảo mật.'
            ]);
        }

        $request->validate([
            'dia_chi_vi' => 'required|string|size:42|regex:/^0x[a-fA-F0-9]{40}$/',
        ], [
            'dia_chi_vi.required' => 'Vui lòng nhập địa chỉ ví.',
            'dia_chi_vi.size'     => 'Địa chỉ ví phải có độ dài 42 ký tự.',
            'dia_chi_vi.regex'    => 'Địa chỉ ví không đúng định dạng (0x...).',
        ]);

        DB::beginTransaction();
        try {
            ViSinhVien::create([
                'sinh_vien_id' => $user->id,
                'dia_chi_vi'   => $request->dia_chi_vi,
                'trang_thai'   => 1
            ]);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Thiết lập ví Blockchain thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }
}
