<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\NhanVien;
use App\Models\SinhVien;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;
use Carbon\Carbon;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;

class AuthController extends Controller
{
    /**
     * Đăng nhập cho Nhân viên
     */
    public function loginNhanVien(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'mat_khau' => 'required',
        ]);

        $nhanVien = NhanVien::where('email', $request->email)->first();

        if (!$nhanVien || !Hash::check($request->mat_khau, $nhanVien->mat_khau)) {
            return response()->json([
                'status' => false,
                'message' => 'Email hoặc mật khẩu không chính xác.'
            ]);
        }

        if ($nhanVien->chucVu && $nhanVien->chucVu->trang_thai == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Chức vụ đang được bảo trì, vui lòng đăng nhập lại sau.'
            ]);
        }

        $token = $nhanVien->createToken('nhan_vien_token', ['staff'])->plainTextToken;

        // Ép nạp quyền hạn và tạo mảng ID phẳng (Flat array)
        $nhanVien->load(['chucVu.chucNangs']);
        $userData = $nhanVien->toArray();
        
        // Tạo thuộc tính list_quyens chứa danh sách ID quyền
        $userData['list_quyens'] = $nhanVien->chucVu ? $nhanVien->chucVu->chucNangs->pluck('id')->toArray() : [];

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập nhân viên thành công.',
            'data' => [
                'user' => $userData,
                'token' => $token,
                'role' => 'staff'
            ]
        ]);
    }

    /**
     * Đăng nhập cho Sinh viên
     */
    public function loginSinhVien(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'mat_khau' => 'required',
        ]);

        $sinhVien = SinhVien::where('email', $request->email)->first();

        if (!$sinhVien || !Hash::check($request->mat_khau, $sinhVien->mat_khau)) {
            return response()->json([
                'status' => false,
                'message' => 'Email hoặc mật khẩu không chính xác.'
            ]);
        }

        $token = $sinhVien->createToken('sinh_vien_token', ['student'])->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập sinh viên thành công.',
            'data' => [
                'user' => $sinhVien->load('viSinhVien'),
                'token' => $token,
                'role' => 'student'
            ]
        ]);
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đăng xuất thành công.'
        ]);
    }

    /**
     * Quên mật khẩu - Gửi Link khôi phục
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {

        $email = $request->email;
        $type = null;
        $user = NhanVien::where('email', $email)->first();
        
        if ($user) {
            $type = 'admin';
        } else {
            $user = SinhVien::where('email', $email)->first();
            if ($user) {
                $type = 'sinh-vien';
            }
        }

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email không tồn tại trong hệ thống.'
            ]);
        }

        // Tạo Token dài
        $token = Str::random(64);

        // Lưu hoặc cập nhật vào bảng password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Tạo URL cho Frontend (Mặc định dùng domain hiện tại hoặc cấu hình APP_URL)
        // Lưu ý: Cần cấu hình FRONTEND_URL trong .env nếu chạy trên các domain khác nhau
        $frontendUrl = env('FRONTEND_URL', 'http://blockdar.vercel.app');
        $url = $frontendUrl . "/$type/reset-password?token=$token&email=" . urlencode($email);

        // Gửi Email
        try {
            Mail::to($email)->send(new ForgotPasswordMail($url));
            return response()->json([
                'status' => true,
                'message' => 'Đã gửi link khôi phục mật khẩu vào Email của bạn.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi gửi email: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Đặt lại mật khẩu mới
     */
    public function resetPassword(ResetPasswordRequest $request)
    {

        $reset = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->where('token', $request->token)
                    ->first();

        if (!$reset) {
            return response()->json([
                'status' => false,
                'message' => 'Token không hợp lệ hoặc Email không chính xác.'
            ]);
        }

        // Kiểm tra hết hạn (ví dụ: 5 phút)
        if (Carbon::parse($reset->created_at)->addMinutes(5)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'status' => false,
                'message' => 'Link khôi phục đã hết hạn. Vui lòng yêu cầu lại!'
            ]);
        }

        // Cập nhật mật khẩu cho user tương ứng
        $user = NhanVien::where('email', $request->email)->first();
        if (!$user) {
            $user = SinhVien::where('email', $request->email)->first();
        }

        if ($user) {
            $user->mat_khau = Hash::make($request->mat_khau);
            $user->save();

            // Xóa token sau khi dùng
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Mật khẩu đã được cập nhật thành công.'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy người dùng.'
        ]);
    }

    /**
     * Đăng xuất khỏi tất cả các thiết bị
     */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đăng xuất khỏi tất cả các thiết bị thành công.'
        ]);
    }
}
