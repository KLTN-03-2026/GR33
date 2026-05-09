<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BangDiem;
use App\Models\DuAn;
use App\Models\ChungChi;
use App\Models\LichSuPheDuyet;
use App\Models\ThongBao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PheDuyetController extends Controller
{
    /**
     * Lấy danh sách yêu cầu đúc NFT (trang_thai = 2)
     */
    public function getListNFT()
    {
        $bang_diem = BangDiem::with('sinhVien', 'lopHoc.monHoc')->where('trang_thai', 2)
            ->whereDoesntHave('nftVanBang', function($q) {
                $q->whereNotNull('chu_ky_so');
            })->get()->map(function($item) {
            $item->loai_ho_so = 'Bảng điểm';
            $item->ten_ho_so = 'Bảng điểm lớp ' . ($item->lopHoc->ten_lop ?? 'N/A');
            return $item;
        });

        $du_an = DuAn::with('sinhVien')->where('trang_thai', 2)
            ->whereDoesntHave('nftVanBang', function($q) {
                $q->whereNotNull('chu_ky_so');
            })->get()->map(function($item) {
            $item->loai_ho_so = 'Dự án';
            $item->ten_ho_so = $item->ten_du_an;
            return $item;
        });

        $chung_chi = ChungChi::with('sinhVien', 'donViCap')->where('trang_thai', 2)
            ->whereDoesntHave('nftVanBang', function($q) {
                $q->whereNotNull('chu_ky_so');
            })->get()->map(function($item) {
            $item->loai_ho_so = 'Chứng chỉ';
            $item->ten_ho_so = $item->ten_chung_chi;
            return $item;
        });

        $danh_sach = $bang_diem->concat($du_an)->concat($chung_chi);

        return response()->json([
            'status' => true,
            'data'   => $danh_sach
        ]);
    }

    /**
     * Lấy danh sách hồ sơ đã ký, chờ đúc NFT (trang_thai = 0 hoặc 1 của NftVanBang)
     */
    public function getListChoDucNFT()
    {
        $bang_diem = BangDiem::with(['sinhVien', 'lopHoc.monHoc', 'nftVanBang'])->where('trang_thai', 2)
            ->whereHas('nftVanBang', function ($q) {
                $q->whereNotNull('chu_ky_so')
                  ->whereIn('trang_thai', [0, 1]); // PENDING hoặc MINTING
            })->get()->map(function($item) {
                $item->loai_ho_so = 'Bảng điểm';
                $item->ten_ho_so = 'Bảng điểm lớp ' . ($item->lopHoc->ten_lop ?? 'N/A');
                return $item;
            });

        $du_an = DuAn::with(['sinhVien', 'nftVanBang'])->where('trang_thai', 2)
            ->whereHas('nftVanBang', function ($q) {
                $q->whereNotNull('chu_ky_so')
                  ->whereIn('trang_thai', [0, 1]);
            })->get()->map(function($item) {
                $item->loai_ho_so = 'Dự án';
                $item->ten_ho_so = $item->ten_du_an;
                return $item;
            });

        $chung_chi = ChungChi::with(['sinhVien', 'donViCap', 'nftVanBang'])->where('trang_thai', 2)
            ->whereHas('nftVanBang', function ($q) {
                $q->whereNotNull('chu_ky_so')
                  ->whereIn('trang_thai', [0, 1]);
            })->get()->map(function($item) {
                $item->loai_ho_so = 'Chứng chỉ';
                $item->ten_ho_so = $item->ten_chung_chi;
                return $item;
            });

        $danh_sach = $bang_diem->concat($du_an)->concat($chung_chi);

        return response()->json([
            'status' => true,
            'data'   => $danh_sach
        ]);
    }

    /**
     * Lấy 5 thông báo mới nhất (đang chờ duyệt)
     */
    public function getNewNotifications()
    {
        // Lấy 5 thông báo mới nhất chưa đọc của bộ phận Admin
        $thong_bao = ThongBao::whereNull('sinh_vien_id')
            ->where(function($q) {
                $q->where('nhan_vien_id', Auth::guard('sanctum')->id())
                  ->orWhereNull('nhan_vien_id');
            })
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $thong_bao,
            'tong'   => ThongBao::whereNull('sinh_vien_id')->where('is_read', false)->count()
        ]);
    }

    /**
     * Lấy danh sách yêu cầu duyệt dữ liệu mới (is_phe_duyet = 0)
     */
    public function getListData()
    {
        $du_an = DuAn::with('sinhVien')->where('is_phe_duyet', 0)->get()->map(function($item) {
            $item->loai_ho_so = 'Dự án';
            $item->ten_ho_so = $item->ten_du_an;
            return $item;
        });

        $chung_chi = ChungChi::with('sinhVien', 'donViCap')->where('is_phe_duyet', 0)->get()->map(function($item) {
            $item->loai_ho_so = 'Chứng chỉ';
            $item->ten_ho_so = $item->ten_chung_chi;
            return $item;
        });

        $danh_sach = $du_an->concat($chung_chi);

        return response()->json([
            'status' => true,
            'data'   => $danh_sach
        ]);
    }

    /**
     * Xử lý phê duyệt/từ chối dữ liệu (is_phe_duyet)
     */
    public function handleData(Request $r)
    {
        $id = $r->id;
        $loai = $r->loai; // 'Dự án' or 'Chứng chỉ'
        $hanh_dong = $r->hanh_dong; // 1: Duyet, 2: Tu choi
        $ly_do = $r->ly_do;

        DB::beginTransaction();
        try {
            $model = null;
            if ($loai == 'Dự án') $model = DuAn::find($id);
            else $model = ChungChi::find($id);

            if (!$model) {
                return response()->json(['status' => false, 'message' => 'Không tìm thấy hồ sơ!']);
            }

            $model->is_phe_duyet = $hanh_dong;
            if ($hanh_dong == 2) {
                $model->ghi_chu_tu_choi = $ly_do;
            } else {
                $model->ghi_chu_tu_choi = null;
            }
            $model->save();

            // Ghi lịch sử
            LichSuPheDuyet::create([
                'admin_id'       => Auth::id() ?? 1,
                'model_type'     => get_class($model),
                'model_id'       => $id,
                'loai_phe_duyet' => 'DU_LIEU',
                'trang_thai_moi' => $hanh_dong,
                'ly_do'          => $ly_do
            ]);

            // Gửi thông báo cho Sinh viên về kết quả duyệt dữ liệu
            ThongBao::create([
                'sinh_vien_id' => $model->sinh_vien_id,
                'tieu_de'      => $hanh_dong == 1 ? 'Hồ sơ đã được duyệt' : 'Hồ sơ bị từ chối',
                'noi_dung'     => 'Yêu cầu duyệt ' . $loai . ' của bạn đã ' . ($hanh_dong == 1 ? 'được phê duyệt thành công.' : 'bị từ chối. Lý do: ' . $ly_do),
                'link'         => $loai == 'Dự án' ? '/sinh-vien/du-an' : '/sinh-vien/chung-chi',
                'loai'         => $hanh_dong == 1 ? 'success' : 'danger'
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => $hanh_dong == 1 ? 'Phê duyệt dữ liệu thành công!' : 'Đã từ chối yêu cầu dữ liệu!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Xử lý từ chối NFT (Chuyển trang_thai về 0)
     */
    public function rejectNFT(Request $r)
    {
        $id = $r->id;
        $loai = $r->loai;
        $ly_do = $r->ly_do;

        DB::beginTransaction();
        try {
            $model = null;
            if ($loai == 'Bảng điểm') $model = BangDiem::find($id);
            elseif ($loai == 'Dự án') $model = DuAn::find($id);
            else $model = ChungChi::find($id);

            if (!$model) {
                return response()->json(['status' => false, 'message' => 'Không tìm thấy hồ sơ!']);
            }

            $model->trang_thai = 0; // Về chưa đúc
            $model->is_locked = false; // Mở khóa để sinh viên có thể sửa lại
            $model->ghi_chu_tu_choi = $ly_do;
            $model->save();

            // Ghi lịch sử
            LichSuPheDuyet::create([
                'admin_id'       => Auth::id() ?? 1,
                'model_type'     => get_class($model),
                'model_id'       => $id,
                'loai_phe_duyet' => 'NFT',
                'trang_thai_moi' => 2, // Tu choi
                'ly_do'          => $ly_do
            ]);

            // Gửi thông báo cho Sinh viên về việc từ chối NFT
            ThongBao::create([
                'sinh_vien_id' => $model->sinh_vien_id,
                'tieu_de'      => 'Yêu cầu đúc NFT bị từ chối',
                'noi_dung'     => 'Yêu cầu đúc NFT cho ' . $loai . ' đã bị Admin từ chối. Lý do: ' . $ly_do,
                'link'         => $loai == 'Bảng điểm' ? '/sinh-vien/bang-diem' : ($loai == 'Dự án' ? '/sinh-vien/du-an' : '/sinh-vien/chung-chi'),
                'loai'         => 'danger'
            ]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Đã từ chối cấp NFT thành công!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
