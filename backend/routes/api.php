<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\SinhVienController;
use App\Http\Controllers\Api\ChucVuController;
use App\Http\Controllers\Api\NhanVienController;
use App\Http\Controllers\Api\PhongBanController;
use App\Http\Controllers\Api\MonHocController;
use App\Http\Controllers\Api\LopHocController;
use App\Http\Controllers\Api\BangDiemController;
use App\Http\Controllers\Api\ChungChiController;
use App\Http\Controllers\Api\DuAnController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PhanQuyenController;
use App\Http\Controllers\Api\ChucNangController;
use App\Http\Controllers\Api\DonViCapController;
use App\Http\Controllers\Api\NftController;
use App\Http\Controllers\Api\ThongKeController;
use App\Http\Controllers\Api\ThongBaoController;
use App\Http\Controllers\Api\PheDuyetController;
use App\Http\Controllers\Api\SinhVienDuAnController;
use App\Http\Controllers\Api\SinhVienChungChiController;
use App\Http\Controllers\Api\SinhVienBangDiemController;
use App\Http\Controllers\Api\SinhVienLopHocController;
use App\Http\Controllers\Api\SinhVienMonHocController;

// 1. PUBLIC ROUTES
Route::post('/login/nhan-vien', [AuthController::class, 'loginNhanVien']);
Route::post('/login/sinh-vien', [AuthController::class, 'loginSinhVien']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/nft/metadata/{tokenId}', [NftController::class, 'layThongTinMetadata']);
Route::get('/nft/trace/{tokenId}', [NftController::class, 'truyVetNft']);


// 2. AUTH ROUTES (General)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// 3. SINH VIÊN ROUTES
Route::prefix('sinh-vien')->middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [SinhVienController::class, 'getProfile']);
    Route::post('/update-profile', [SinhVienController::class, 'updateProfile']);
    Route::post('/change-password', [SinhVienController::class, 'changePassword']);
    Route::post('/update-wallet', [SinhVienController::class, 'updateWallet']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::prefix('nft')->group(function () {
        Route::post('/request', [NftController::class, 'guiYeuCauDucNft']);
    });

    Route::prefix('du-ans')->group(function () {
        Route::get('/get-data', [SinhVienDuAnController::class, 'getMyDuAn']);
        Route::post('/create', [SinhVienDuAnController::class, 'createDuAn']);
        Route::post('/update/{id}', [SinhVienDuAnController::class, 'updateDuAn']);
    });

    Route::prefix('chung-chis')->group(function () {
        Route::get('/get-data', [SinhVienChungChiController::class, 'getMyChungChi']);
        Route::post('/create', [SinhVienChungChiController::class, 'createChungChi']);
        Route::post('/update/{id}', [SinhVienChungChiController::class, 'updateChungChi']);
    });

    Route::get('/bang-diems', [SinhVienBangDiemController::class, 'getMyBangDiem']);

    Route::prefix('thong-bao')->group(function () {
        Route::get('/get-new', [ThongBaoController::class, 'getListSinhVien']);
        Route::post('/mark-read', [ThongBaoController::class, 'markAsRead']);
        Route::post('/read-all', [ThongBaoController::class, 'readAllSinhVien']);
    });

    Route::get('/don-vi-caps/all', [DonViCapController::class, 'getDataDonViCap']);

    // Môn học & Lớp học nâng cao
    Route::get('/mon-hocs', [SinhVienMonHocController::class, 'getData']);
    Route::prefix('lop-hocs')->group(function () {
        Route::get('/all', [SinhVienLopHocController::class, 'getData']);
        Route::post('/dang-ky', [SinhVienLopHocController::class, 'dangKy']);
        Route::post('/huy-dang-ky', [SinhVienLopHocController::class, 'huyDangKy']);
    });
});


// 4. ADMIN ROUTES (Unified Group)
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [NhanVienController::class, 'getProfile']);
    Route::post('/update-profile', [NhanVienController::class, 'updateProfile']);
    Route::post('/change-password', [NhanVienController::class, 'changePassword']);
    Route::post('/update-wallet', [NhanVienController::class, 'updateWallet']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    // Hệ thống & Phân quyền
    Route::prefix('chuc-vus')->group(function () {
        Route::get('/get-data', [ChucVuController::class, 'getDataChucVu'])->middleware('check.permission:11');
        Route::post('/create', [ChucVuController::class, 'createChucVu'])->middleware('check.permission:12');
        Route::put('/update/{chuc_vu}', [ChucVuController::class, 'updateChucVu'])->middleware('check.permission:13');
        Route::delete('/delete/{chuc_vu}', [ChucVuController::class, 'deleteChucVu'])->middleware('check.permission:13');
        Route::get('/search', [ChucVuController::class, 'searchChucVu']);
        Route::get('/get-data-allowed', [ChucVuController::class, 'getDataChucVuAllowed']);
        Route::post('/change-status', [ChucVuController::class, 'changeStatusChucVu'])->middleware('check.permission:13');
    });

    Route::prefix('phan-quyen')->middleware('check.permission:14')->group(function () {
        Route::get('/get-data', [PhanQuyenController::class, 'getDataPhanQuyen']);
        Route::post('/create', [PhanQuyenController::class, 'createPhanQuyen']);
        Route::post('/delete', [PhanQuyenController::class, 'deletePhanQuyen']);
        Route::post('/update', [PhanQuyenController::class, 'updatePhanQuyen']);
    });

    Route::prefix('chuc-nang')->middleware('check.permission:14')->group(function () {
        Route::get('/get-data', [ChucNangController::class, 'getDataChucNang']);
    });

    // Người dùng & Tổ chức
    Route::prefix('phong-bans')->group(function () {
        Route::get('/get-data', [PhongBanController::class, 'getDataPhongBan'])->middleware('check.permission:21');
        Route::post('/create', [PhongBanController::class, 'createPhongBan'])->middleware('check.permission:22');
        Route::get('/detail/{phong_ban}', [PhongBanController::class, 'getDetailPhongBan'])->middleware('check.permission:21');
        Route::put('/update/{phong_ban}', [PhongBanController::class, 'updatePhongBan'])->middleware('check.permission:22');
        Route::delete('/delete/{phong_ban}', [PhongBanController::class, 'deletePhongBan'])->middleware('check.permission:22');
    });

    Route::prefix('nhan-viens')->group(function () {
        Route::get('/get-data', [NhanVienController::class, 'getDataNhanVien'])->middleware('check.permission:23');
        Route::post('/create', [NhanVienController::class, 'createNhanVien'])->middleware('check.permission:24');
        Route::get('/detail/{nhan_vien}', [NhanVienController::class, 'getDetailNhanVien'])->middleware('check.permission:23');
        Route::put('/update/{nhan_vien}', [NhanVienController::class, 'updateNhanVien'])->middleware('check.permission:25');
        Route::delete('/delete/{nhan_vien}', [NhanVienController::class, 'deleteNhanVien'])->middleware('check.permission:25');
        Route::post('/change-status', [NhanVienController::class, 'changeStatusNhanVien'])->middleware('check.permission:25');
    });

    Route::prefix('sinh-viens')->group(function () {
        Route::get('/get-data', [SinhVienController::class, 'getDataSinhVien'])->middleware('check.permission:26');
        Route::post('/create', [SinhVienController::class, 'createSinhVien'])->middleware('check.permission:27');
        Route::get('/detail/{sinh_vien}', [SinhVienController::class, 'getDetailSinhVien'])->middleware('check.permission:26');
        Route::put('/update/{sinh_vien}', [SinhVienController::class, 'updateSinhVien'])->middleware('check.permission:27');
        Route::delete('/delete/{sinh_vien}', [SinhVienController::class, 'deleteSinhVien'])->middleware('check.permission:27');
    });

    // Khoa & Đào tạo
    Route::prefix('mon-hocs')->group(function () {
        Route::get('/get-data', [MonHocController::class, 'getDataMonHoc'])->middleware('check.permission:31');
        Route::post('/create', [MonHocController::class, 'createMonHoc'])->middleware('check.permission:32');
        Route::get('/detail/{mon_hoc}', [MonHocController::class, 'getDetailMonHoc'])->middleware('check.permission:31');
        Route::put('/update/{mon_hoc}', [MonHocController::class, 'updateMonHoc'])->middleware('check.permission:32');
        Route::delete('/delete/{mon_hoc}', [MonHocController::class, 'deleteMonHoc'])->middleware('check.permission:32');
    });

    Route::prefix('lop-hocs')->group(function () {
        Route::get('/get-data', [LopHocController::class, 'getDataLopHoc'])->middleware('check.permission:33');
        Route::post('/create', [LopHocController::class, 'createLopHoc'])->middleware('check.permission:34');
        Route::get('/detail/{lop_hoc}', [LopHocController::class, 'getDetailLopHoc'])->middleware('check.permission:33');
        Route::put('/update/{lop_hoc}', [LopHocController::class, 'updateLopHoc'])->middleware('check.permission:34');
        Route::delete('/delete/{lop_hoc}', [LopHocController::class, 'deleteLopHoc'])->middleware('check.permission:34');
    });

    Route::prefix('don-vi-caps')->group(function () {
        Route::get('/get-data', [DonViCapController::class, 'getDataDonViCap'])->middleware('check.permission:35');
        Route::post('/create', [DonViCapController::class, 'createDonViCap'])->middleware('check.permission:35');
        Route::get('/detail/{don_vi_cap}', [DonViCapController::class, 'getDetailDonViCap'])->middleware('check.permission:35');
        Route::put('/update/{don_vi_cap}', [DonViCapController::class, 'updateDonViCap'])->middleware('check.permission:35');
        Route::delete('/delete/{don_vi_cap}', [DonViCapController::class, 'deleteDonViCap'])->middleware('check.permission:35');
    });

    // Hồ sơ Văn bằng
    Route::prefix('bang-diems')->group(function () {
        Route::get('/get-data', [BangDiemController::class, 'getDataBangDiem'])->middleware('check.permission:41');
        Route::post('/create', [BangDiemController::class, 'createBangDiem'])->middleware(['check.lock', 'check.permission:42']);
        Route::get('/detail/{bang_diem}', [BangDiemController::class, 'getDetailBangDiem'])->middleware('check.permission:41');
        Route::put('/update/{bang_diem}', [BangDiemController::class, 'updateBangDiem'])->middleware(['check.lock', 'check.permission:43']);
        Route::delete('/delete/{bang_diem}', [BangDiemController::class, 'deleteBangDiem'])->middleware(['check.lock', 'check.permission:44']);
    });

    Route::prefix('chung-chis')->group(function () {
        Route::get('/get-data', [ChungChiController::class, 'getDataChungChi'])->middleware('check.permission:41');
        Route::post('/create', [ChungChiController::class, 'adminCreateChungChi'])->middleware(['check.lock', 'check.permission:42']);
        Route::get('/detail/{chung_chi}', [ChungChiController::class, 'getDetailChungChi'])->middleware('check.permission:41');
        Route::put('/update/{chung_chi}', [ChungChiController::class, 'adminUpdateChungChi'])->middleware(['check.lock', 'check.permission:43']);
        Route::delete('/delete/{chung_chi}', [ChungChiController::class, 'deleteChungChi'])->middleware(['check.lock', 'check.permission:44']);
    });

    Route::prefix('du-ans')->group(function () {
        Route::get('/get-data', [DuAnController::class, 'getDataDuAn'])->middleware('check.permission:41');
        Route::post('/create', [DuAnController::class, 'adminCreateDuAn'])->middleware(['check.lock', 'check.permission:42']);
        Route::get('/detail/{du_an}', [DuAnController::class, 'getDetailDuAn'])->middleware('check.permission:41');
        Route::put('/update/{du_an}', [DuAnController::class, 'adminUpdateDuAn'])->middleware(['check.lock', 'check.permission:43']);
        Route::delete('/delete/{du_an}', [DuAnController::class, 'deleteDuAn'])->middleware(['check.lock', 'check.permission:44']);
    });

    // Blockchain & Phê duyệt
    Route::prefix('phe-duyet')->middleware('check.permission:51')->group(function () {
        Route::get('/nft', [PheDuyetController::class, 'getListNFT']);
        Route::get('/cho-duc-nft', [PheDuyetController::class, 'getListChoDucNFT']);
        Route::get('/du-lieu', [PheDuyetController::class, 'getListData']);
        Route::post('/xu-ly-du-lieu', [PheDuyetController::class, 'handleData'])->middleware('check.permission:54');
        Route::post('/tu-choi-nft', [PheDuyetController::class, 'rejectNFT']);
        Route::get('/thong-bao-moi', [PheDuyetController::class, 'getNewNotifications']);
    });

    Route::prefix('nft')->group(function () {
        Route::get('/requests', [NftController::class, 'danhSachYeuCauCho'])->middleware('check.permission:51');
        Route::post('/handle-request', [NftController::class, 'xuLyYeuCau'])->middleware('check.permission:51');
        Route::post('/sign', [NftController::class, 'kySoNft'])->middleware('check.permission:52');
        Route::post('/mint', [NftController::class, 'ducNft'])->middleware('check.permission:52');
        Route::get('/get-data', [NftController::class, 'danhSachNft'])->middleware('check.permission:51');
        Route::post('/revoke', [NftController::class, 'thuHoiNft'])->middleware('check.permission:53'); 
    });

    // Thống kê
    Route::get('/thong-ke', [ThongKeController::class, 'layDuLieuThongKe'])->middleware('check.permission:61');

    Route::prefix('thong-bao')->group(function () {
        Route::get('/get-new', [ThongBaoController::class, 'getListAdmin']);
        Route::post('/mark-read', [ThongBaoController::class, 'markAsRead']);
        Route::post('/read-all', [ThongBaoController::class, 'readAllAdmin']);
    });
});
