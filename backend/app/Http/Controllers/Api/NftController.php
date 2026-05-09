<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BangDiem;
use App\Models\ChungChi;
use App\Models\DuAn;
use App\Models\NftVanBang;
use App\Models\ThongBao;
use App\Models\LichSuGiaoDich;
use App\Services\BlockchainService;
use App\Jobs\VerifyBlockchainTransaction;
use App\Models\SmartContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Web3p\EthereumUtil\Util;
use Elliptic\EC;
use Int64\Int64; // Thư viện đi kèm nếu cần

class NftController extends Controller
{
    /**
     * SINH VIÊN: Gửi yêu cầu đúc NFT cho hồ sơ của mình
     */
    public function guiYeuCauDucNft(Request $request)
    {
        $request->validate([
            'record_id' => 'required|integer',
            'type'      => 'required|in:bang_diem,chung_chi,du_an',
        ]);

        $user = auth()->user();
        
        // KIỂM TRA: Sinh viên phải có ví Blockchain trước khi yêu cầu NFT
        $user->load('viSinhVien');
        if (!$user->viSinhVien || !$user->viSinhVien->dia_chi_vi) {
            return response()->json([
                'status'  => false,
                'message' => 'Tài khoản của bạn chưa được liên kết với ví Blockchain. Vui lòng cài đặt ví tại thông tin cá nhân trước khi thực hiện yêu cầu NFT.'
            ]);
        }

        $record = $this->timHoSoNguon($request->type, $request->record_id);

        if (!$record) {
            return response()->json([
                'status'  => false,
                'message' => 'Bản ghi không tồn tại.'
            ]);
        }

        // Kiểm tra quyền sở hữu (Bản ghi phải thuộc về sinh viên đang đăng nhập)
        if ($record->sinh_vien_id !== $user->id) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn không có quyền yêu cầu cấp NFT cho hồ sơ này.'
            ]);
        }

        // Kiểm tra trạng thái
        if ($record->trang_thai == $record::STATUS_MINTED) {
            return response()->json(['status' => false, 'message' => 'Hồ sơ đã được đúc NFT.']);
        }

        if ($record->is_locked || $record->trang_thai == $record::STATUS_PENDING_MINT) {
            return response()->json(['status' => false, 'message' => 'Hồ sơ đang chờ duyệt hoặc đã bị khóa.']);
        }

        // Kiểm tra Bảng điểm phải có điểm tổng kết mới được phép yêu cầu NFT
        if ($request->type === 'bang_diem' && is_null($record->diem_tong_ket)) {
            return response()->json([
                'status'  => false,
                'message' => 'Bảng điểm chưa hoàn thành (chưa có điểm tổng kết). Vui lòng đợi giảng viên cập nhật điểm trước khi yêu cầu NFT.'
            ]);
        }

        // Khóa dữ liệu và chờ duyệt
        $record->update([
            'is_locked'  => true,
            'trang_thai' => $record::STATUS_PENDING_MINT,
        ]);

        // Gửi thông báo cho Admin
        $loaiTen = $request->type == 'bang_diem' ? 'Bảng điểm' : ($request->type == 'chung_chi' ? 'Chứng chỉ' : 'Dự án');
        ThongBao::create([
            'nhan_vien_id' => null,
            'tieu_de'      => 'Yêu cầu đúc NFT mới',
            'noi_dung'     => 'Sinh viên ' . $user->ho_ten . ' vừa gửi yêu cầu đúc NFT cho ' . $loaiTen . '.',
            'link'         => '/admin/phe-duyet',
            'loai'         => 'info'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Yêu cầu của bạn đã được gửi tới Quản trị viên và dữ liệu đã được khóa.',
        ]);
    }

    /**
     * ADMIN: Liệt kê các yêu cầu đang chờ duyệt
     */
    public function danhSachYeuCauCho()
    {
        $bangDiems = BangDiem::where('trang_thai', BangDiem::STATUS_PENDING_MINT)->with('sinhVien')->get()->map(function($item) {
            $item->type = 'bang_diem';
            return $item;
        });
        $chungChis = ChungChi::where('trang_thai', ChungChi::STATUS_PENDING_MINT)->with('sinhVien')->get()->map(function($item) {
            $item->type = 'chung_chi';
            return $item;
        });
        $duAns = DuAn::where('trang_thai', DuAn::STATUS_PENDING_MINT)->with('sinhVien')->get()->map(function($item) {
            $item->type = 'du_an';
            return $item;
        });

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách yêu cầu chờ duyệt thành công.',
            'data'    => $bangDiems->concat($chungChis)->concat($duAns)
        ]);
    }

    /**
     * ADMIN: Duyệt hoặc Từ chối yêu cầu
     */
    public function xuLyYeuCau(Request $request)
    {
        $request->validate([
            'record_id' => 'required|integer',
            'type'      => 'required|in:bang_diem,chung_chi,du_an',
            'action'    => 'required|in:approve,reject',
            'reason'    => 'nullable|string',
        ]);

        $record = $this->timHoSoNguon($request->type, $request->record_id);

        if (!$record || $record->trang_thai != $record::STATUS_PENDING_MINT) {
            return response()->json(['status' => false, 'message' => 'Yêu cầu không hợp lệ.']);
        }

        $loaiTen = $request->type == 'bang_diem' ? 'Bảng điểm' : ($request->type == 'chung_chi' ? 'Chứng chỉ' : 'Dự án');

        if ($request->action === 'approve') {
            // Duyệt: Băm dữ liệu và tạo bản ghi NFT sẵn sàng để đúc
            $hash = $this->taoMaHash($record, $request->type);

            $contract = SmartContract::where('trang_thai', 1)->first();
            if (!$contract) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy hợp đồng thông minh nào đang hoạt động!'
                ]);
            }

            $nftVanBang = NftVanBang::updateOrCreate(
                [
                    'nftable_type' => get_class($record),
                    'nftable_id'   => $record->id,
                ],
                [
                    'smart_contract_id' => $contract->id,
                    'hash_du_lieu' => $hash,
                    'trang_thai'   => NftVanBang::STATUS_PENDING,
                ]
            );

            $record->update([
                'trang_thai' => 2, // Chờ ký số
                'ghi_chu_tu_choi' => null, // Xóa ghi chú lỗi cũ nếu có
            ]);

            // Thông báo cho sinh viên
            ThongBao::create([
                'sinh_vien_id' => $record->sinh_vien_id,
                'tieu_de'      => 'Yêu cầu NFT được chấp nhận',
                'noi_dung'     => "Yêu cầu cấp NFT cho hồ sơ $loaiTen của bạn đã được Admin phê duyệt. Hồ sơ hiện đang đợi ký số và đúc lên Blockchain.",
                'loai'         => 'success'
            ]);

            return response()->json([
                'status'          => true,
                'message'         => 'Đã duyệt yêu cầu thành công. Hồ sơ sẵn sàng để ký số.',
                'nft_van_bang_id' => $nftVanBang->id,
                'hash'            => $hash
            ]);
        } else {
            // Tu choi: Mở khóa và xóa bản ghi NFT tạm thời nếu có
            $record->is_locked       = false; // Reset khóa
            $record->trang_thai      = $record::STATUS_NOT_MINTED; // Chuyển về trạng thái chưa đúc
            $record->ghi_chu_tu_choi = $request->reason; // Lưu lý do từ chối
            $record->save();

            // Xóa bản ghi NftVanBang liên quan (vì dữ liệu gốc có thể sẽ bị sửa)
            NftVanBang::where('nftable_type', get_class($record))
                      ->where('nftable_id', $record->id)
                      ->delete();

            // Thông báo cho sinh viên
            $lyDo = $request->reason ? " (Lý do: " . $request->reason . ")" : "";
            ThongBao::create([
                'sinh_vien_id' => $record->sinh_vien_id,
                'tieu_de'      => 'Yêu cầu NFT bị từ chối',
                'noi_dung'     => "Yêu cầu cấp NFT cho hồ sơ $loaiTen của bạn đã bị từ chối và hồ sơ đã được mở khóa để bạn có thể chỉnh sửa.$lyDo",
                'loai'         => 'warning'
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Đã từ chối yêu cầu. Hồ sơ đã được mở khóa.',
            ]);
        }
    }

    /**
     * ADMIN: Người có thẩm quyền thực hiện ký số lên mã băm
     */
    public function kySoNft(Request $request)
    {
        $request->validate([
            'nft_van_bang_id' => 'required|integer|exists:nft_van_bangs,id',
            'signature'       => 'required|string',
        ]);

        $user = auth()->user();
        $nftVanBang = NftVanBang::find($request->nft_van_bang_id);
        $masterAddress = env('MASTER_WALLET_ADDRESS');

        if ($nftVanBang->trang_thai != NftVanBang::STATUS_PENDING) {
            return response()->json([
                'status'  => false,
                'message' => 'Hồ sơ này không ở trạng thái chờ ký hoặc đã được đúc thành công.'
            ]);
        }

        if ($nftVanBang->chu_ky_so) {
            return response()->json([
                'status'  => false,
                'message' => 'Hồ sơ này đã được ký số trước đó.'
            ]);
        }

        // XÁC THỰC CHỮ KÝ SỐ (Dùng ví Master chung cho toàn hệ thống)
        $hopLe = $this->xacThucChuKy($nftVanBang->hash_du_lieu, $request->signature, $masterAddress);

        if (!$hopLe) {
            return response()->json([
                'status'  => false,
                'message' => 'Chữ ký không hợp lệ (Không khớp với ví Master của hệ thống). Vui lòng kiểm tra lại thiết bị ký của bạn!'
            ]);
        }

        // Cập nhật chữ ký và Người thực hiện thao tác ký (auth()->id())
        $nftVanBang->update([
            'chu_ky_so'      => $request->signature,
            'nhan_vien_ky_id' => $user->id,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Ký số hồ sơ thành công. Sẵn sàng để đúc NFT lên Blockchain.',
            'data'    => $nftVanBang
        ]);
    }

    /**
     * ADMIN: Thực hiện đúc (Mint) NFT lên Blockchain
     */
    public function ducNft(Request $request, BlockchainService $blockchainService)
    {
        $request->validate([
            'nft_van_bang_id' => 'required|integer|exists:nft_van_bangs,id',
        ]);

        $nftVanBang = NftVanBang::with('nftable')->find($request->nft_van_bang_id);

        if (!$nftVanBang->chu_ky_so) {
            return response()->json([
                'status'  => false,
                'message' => 'Hồ sơ này chưa có chữ ký số xác thực. Không thể đúc NFT.'
            ]);
        }

        if ($nftVanBang->trang_thai == NftVanBang::STATUS_SUCCESS) {
            return response()->json([
                'status'  => false,
                'message' => 'Hồ sơ này đã được đúc NFT thành công trước đó.'
            ]);
        }

        try {
            // Lấy địa chỉ ví của sinh viên sở hữu hồ sơ
            $sinhVien = $nftVanBang->nftable->sinhVien;
            $sinhVien->load('viSinhVien');
            
            if (!$sinhVien->viSinhVien || !$sinhVien->viSinhVien->dia_chi_vi) {
                 return response()->json(['status' => false, 'message' => 'Sinh viên chưa có ví để nhận NFT.']);
            }

            // Lấy Mã ID của nhân viên thực hiện ký (để truy vết On-chain)
            $idNhanVienKy = $nftVanBang->nhan_vien_ky_id;

            $diaChiSinhVien = $sinhVien->viSinhVien->dia_chi_vi;
            $maHashDuLieu = $nftVanBang->hash_du_lieu;
            $chuKy = $nftVanBang->chu_ky_so;

            // 1. Lấy Token ID tiếp theo từ Blockchain để tạo link Metadata
            $tokenIdTiepTheo = $blockchainService->layTokenIdTiepTheo();
            $duongDanToken = url("/api/nft/metadata/" . $tokenIdTiepTheo);

            // 2. Lấy dữ liệu hồ sơ chi tiết để lưu lên Blockchain
            $typeShort = '';
            if ($nftVanBang->nftable_type === BangDiem::class) $typeShort = 'bang_diem';
            elseif ($nftVanBang->nftable_type === ChungChi::class) $typeShort = 'chung_chi';
            elseif ($nftVanBang->nftable_type === DuAn::class) $typeShort = 'du_an';
            
            $metadataJson = $this->taoJsonMetadata($nftVanBang->nftable, $typeShort);

            // 3. Gọi Blockchain Service để gửi giao dịch với 6 tham số (thêm metadata)
            $ketQua = $blockchainService->ducNftOnChain($diaChiSinhVien, $chuKy, $maHashDuLieu, $duongDanToken, $idNhanVienKy, $metadataJson);

            $maGiaoDich = $ketQua['transaction_hash'];

            // 4. Khóa hồ sơ gốc trong quá trình Blockchain xử lý
            $nftVanBang->nftable->update(['is_locked' => true]);

            // Cập nhật trạng thái ĐANG ĐÚC
            $nftVanBang->update([
                'tx_hash_thanh_cong' => $maGiaoDich,
                'trang_thai'         => NftVanBang::STATUS_MINTING,
            ]);

            // Ghi lịch sử giao dịch ban đầu (Đang đúc)
            LichSuGiaoDich::create([
                'nft_van_bang_id' => $nftVanBang->id,
                'nguoi_thuc_hien_id' => $nftVanBang->nhan_vien_ky_id,
                'hanh_dong' => 'MINT_START',
                'transaction_hash' => $maGiaoDich,
                'trang_thai' => 1,
            ]);

            // Đẩy Job xác thực giao dịch vào hàng chờ
            VerifyBlockchainTransaction::dispatch($nftVanBang->id);

            return response()->json([
                'status'  => true,
                'message' => 'Đã gửi lệnh đúc NFT lên Blockchain. Hệ thống đang đợi xác nhận giao dịch.',
                'tx_hash' => $maGiaoDich
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi kết nối Blockchain: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * TẠO MÃ HASH: Băm dữ liệu hồ sơ để thực hiện ký số
     */
    private function taoMaHash($record, $type)
    {
        $dataToHash = $this->layDuLieuHoSo($record, $type);
        // Chuyển sang JSON và Băm bằng SHA-256
        return hash('sha256', json_encode($dataToHash));
    }

    /**
     * TẠO JSON METADATA: Lấy dữ liệu hồ sơ dưới dạng JSON để lưu On-chain
     */
    private function taoJsonMetadata($record, $type)
    {
        $data = $this->layDuLieuHoSo($record, $type);
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Lấy mảng dữ liệu đặc thù của từng loại hồ sơ
     */
    private function layDuLieuHoSo($record, $type)
    {
        $data = [];
        switch ($type) {
            case 'bang_diem':
                $data = [
                    'sinh_vien_id'   => $record->sinh_vien_id,
                    'lop_hoc_id'     => $record->lop_hoc_id,
                    'diem_qua_trinh' => $record->diem_qua_trinh,
                    'diem_cuoi_ky'   => $record->diem_cuoi_ky,
                    'diem_tong_ket'  => $record->diem_tong_ket,
                    'diem_chu'       => $record->diem_chu,
                    'diem_he_4'      => $record->diem_he_4,
                ];
                break;
            case 'chung_chi':
                $data = [
                    'ma_chung_chi'   => $record->ma_chung_chi,
                    'ten_chung_chi'  => $record->ten_chung_chi,
                    'loai_chung_chi' => $record->loai_chung_chi,
                    'sinh_vien_id'   => $record->sinh_vien_id,
                    'don_vi_cap_id'  => $record->don_vi_cap_id,
                    'diem_so'        => $record->diem_so,
                    'xep_loai'       => $record->xep_loai,
                    'file_dinh_kem'  => $record->file_dinh_kem,
                    'ngay_cap'       => $record->ngay_cap,
                    'ngay_het_han'   => $record->ngay_het_han,
                ];
                break;
            case 'du_an':
                $data = [
                    'ma_du_an'     => $record->ma_du_an,
                    'ten_du_an'    => $record->ten_du_an,
                    'mo_ta'        => $record->mo_ta,
                    'sinh_vien_id' => $record->sinh_vien_id,
                    'link_du_an'   => $record->link_du_an,
                ];
                break;
        }
        return $data;
    }

    /**
     * Tạo danh sách thuộc tính chuẩn tắc (Sử dụng chung cho Tạo Metadata và Đối soát)
     */
    private function layThuocTinhHoSo($nftVanBang)
    {
        $hoSoNguon = $nftVanBang->nftable;
        $sinhVien = $hoSoNguon ? $hoSoNguon->sinhVien : null;
        $tenSinhVien = $sinhVien->ho_ten ?? 'Unknown Student';

        $thuocTinh = [
            ['trait_type' => 'Loại hồ sơ', 'value' => class_basename($nftVanBang->nftable_type)],
            ['trait_type' => 'Họ tên Sinh viên', 'value' => $tenSinhVien],
            ['trait_type' => 'Mã sinh viên', 'value' => $sinhVien->ma_sinh_vien ?? 'N/A'],
        ];

        if ($hoSoNguon instanceof BangDiem) {
            $lopHoc = $hoSoNguon->lopHoc;
            $monHoc = $lopHoc ? $lopHoc->monHoc : null;
            
            $thuocTinh[] = ['trait_type' => 'Hoàn thành môn học', 'value' => $monHoc->ten_mon_hoc ?? 'N/A'];
            $thuocTinh[] = ['trait_type' => 'Lớp học', 'value' => $lopHoc->ten_lop_hoc ?? 'N/A'];
            $thuocTinh[] = ['display_type' => 'number', 'trait_type' => 'Điểm quá trình', 'value' => (float)$hoSoNguon->diem_qua_trinh];
            $thuocTinh[] = ['display_type' => 'number', 'trait_type' => 'Điểm thi cuối kỳ', 'value' => (float)$hoSoNguon->diem_cuoi_ky];
            $thuocTinh[] = ['display_type' => 'number', 'trait_type' => 'Điểm tổng kết', 'value' => (float)$hoSoNguon->diem_tong_ket];
            $thuocTinh[] = ['trait_type' => 'Xếp loại (Hệ 4)', 'value' => (string)$hoSoNguon->diem_he_4];
            $thuocTinh[] = ['trait_type' => 'Xếp loại tín chỉ', 'value' => $hoSoNguon->diem_chu];
        } 
        elseif ($hoSoNguon instanceof ChungChi) {
            $thuocTinh[] = ['trait_type' => 'Tên chứng chỉ', 'value' => $hoSoNguon->ten_chung_chi];
            $thuocTinh[] = ['trait_type' => 'Mã hiệu chứng chỉ', 'value' => $hoSoNguon->ma_chung_chi];
            $thuocTinh[] = ['trait_type' => 'Loại chứng chỉ', 'value' => $hoSoNguon->loai_chung_chi];
            $thuocTinh[] = ['trait_type' => 'Đơn vị/Cơ quan cấp', 'value' => $hoSoNguon->donViCap->ten_don_vi ?? 'N/A'];
            $thuocTinh[] = ['display_type' => 'date', 'trait_type' => 'Ngày cấp', 'value' => strtotime($hoSoNguon->ngay_cap)];
            $thuocTinh[] = ['trait_type' => 'Đánh giá xếp loại', 'value' => $hoSoNguon->xep_loai];
        }
        elseif ($hoSoNguon instanceof DuAn) {
            $thuocTinh[] = ['trait_type' => 'Tên dự án/Đề tài', 'value' => $hoSoNguon->ten_du_an];
            $thuocTinh[] = ['trait_type' => 'Mã dự án', 'value' => $hoSoNguon->ma_du_an];
            $thuocTinh[] = ['trait_type' => 'Tham chiếu nội dung', 'value' => $hoSoNguon->link_du_an];
        }

        return $thuocTinh;
    }

    /**
     * Dịch các trường trong Database sang nhãn Tiếng Việt thân thiện
     */
    private function dichNhauTruongDuLieu($type, $key)
    {
        $map = [
            'sinh_vien_id' => 'Mã ID Sinh viên',
        ];

        if ($type === 'bang_diem') {
            $map = array_merge($map, [
                'lop_hoc_id'     => 'Mã ID Lớp học',
                'diem_qua_trinh' => 'Điểm quá trình (Hệ 10)',
                'diem_cuoi_ky'   => 'Điểm thi cuối kỳ (Hệ 10)',
                'diem_tong_ket'  => 'Điểm tổng kết môn',
                'diem_chu'       => 'Xếp loại tín chỉ',
                'diem_he_4'      => 'Xếp loại (Hệ 4)',
            ]);
        } elseif ($type === 'chung_chi') {
            $map = array_merge($map, [
                'ma_chung_chi'   => 'Mã hiệu chứng chỉ',
                'ten_chung_chi'  => 'Tên loại chứng chỉ',
                'loai_chung_chi' => 'Phân loại chứng chỉ',
                'don_vi_cap_id'  => 'ID Đơn vị cấp',
                'diem_so'        => 'Điểm số/Kết quả',
                'xep_loai'       => 'Đánh giá xếp loại',
                'file_dinh_kem'  => 'Tài liệu Scanned',
                'ngay_cap'       => 'Ngày cấp / Ban hành',
                'ngay_het_han'   => 'Ngày hết hiệu lực',
            ]);
        } elseif ($type === 'du_an') {
            $map = array_merge($map, [
                'ma_du_an'     => 'Mã số dự án',
                'ten_du_an'    => 'Tên dự án / Đề tài',
                'mo_ta'        => 'Mô tả tóm tắt nội dung',
                'link_du_an'   => 'Tham chiếu kho lưu trữ',
            ]);
        }

        return $map[$key] ?? $key;
    }

    /**
     * XÁC THỰC CHỮ KÝ SỐ: Trích xuất địa chỉ ví từ mã băm và chữ ký
     */
    private function xacThucChuKy($thongDiep, $chuKy, $diaChiMaster)
    {
        $util = new Util();
        
        // 1. Chuẩn hóa message: Frontend ethers.getBytes() truyền dữ liệu dưới dạng binary 32 bytes
        $thongDiepHex = str_replace('0x', '', $thongDiep);
        $thongDiepNhiPhan = hex2bin($thongDiepHex);
        $thongDiepEthereum = "\x19Ethereum Signed Message:\n" . strlen($thongDiepNhiPhan) . $thongDiepNhiPhan;
        $maBam = $util->sha3($thongDiepEthereum);

        // 2. Chế biến signature
        $chuKy = str_replace('0x', '', $chuKy);
        if (strlen($chuKy) != 130) return false;

        $r = substr($chuKy, 0, 64);
        $s = substr($chuKy, 64, 64);
        $vHex = substr($chuKy, 128, 2);
        $v = hexdec($vHex);

        // 3. Xử lý Recovery ID (v) cho chuẩn Ethereum
        if ($v < 27) $v += 27;
        $maKhoiPhuc = $v - 27;

        try {
            // 4. Giải mã Public Key
            $ec = new EC('secp256k1');
            $khoaCongKhai = $ec->recoverPubKey($maBam, ['r' => $r, 's' => $s], $maKhoiPhuc);
            $khoaCongKhaiHex = $khoaCongKhai->encode('hex');
            
            // 5. Chuyển Public Key thành Address
            $recoveredAddress = '0x' . $util->publicKeyToAddress($khoaCongKhaiHex);
            
            // Xử lý nếu bị lặp 0x0x
            $recoveredAddress = str_replace('0x0x', '0x', $recoveredAddress);

            // LOG ĐỂ DEBUG
            Log::info("Xac thuc Chu ky NFT:", [
                'expected_address' => $diaChiMaster,
                'recovered_address' => $recoveredAddress,
                'message_hash' => $maBam,
            ]);

            return strtolower($recoveredAddress) === strtolower($diaChiMaster);
        } catch (\Exception $e) {
            Log::error("Signature Verification Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Trả về Metadata chuẩn ERC721 cho một Token ID.
     */
    // 5. Public Metadata API cho NFT
    public function layThongTinMetadata($tokenId)
    {
        $nftVanBang = NftVanBang::where('token_id', $tokenId)->first();

        if (!$nftVanBang) {
            return response()->json([
                'status' => false,
                'message' => 'NFT not found'
            ]);
        }

        $hoSoNguon = $nftVanBang->nftable; // Polymorphic relation (BangDiem, ChungChi, DuAn)
        if (!$hoSoNguon) {
            return response()->json([
                'status' => false,
                'message' => 'Source record not found'
            ]);
        }

        $sinhVien = $hoSoNguon->sinhVien; // CamelCase matches Model definition

        $tenNft = "Academic Record #" . $tokenId;
        $tenSinhVien = $sinhVien->ho_ten ?? 'Unknown Student';
        $moTa = "Chứng thực hồ sơ học tập cho sinh viên " . $tenSinhVien;
        
        $thuocTinh = $this->layThuocTinhHoSo($nftVanBang);

        return response()->json([
            'name' => $tenNft,
            'description' => $moTa,
            'image' => ($sinhVien && $sinhVien->hinh_anh) ? url($sinhVien->hinh_anh) : 'https://nft.academic.edu/default-nft.png',
            'attributes' => $thuocTinh,
            'external_url' => url('/'),
        ]);
    }

    /**
     * TRUY VẾT NFT: Lấy thông tin chi tiết về người ký và nguồn gốc của NFT.
     * API công khai phục vụ xác thực văn bằng.
     */
    public function truyVetNft($tokenId, BlockchainService $blockchainService)
    {
        // 1. Tìm NFT với trạng thái Thành công HOẶC Đã thu hồi
        $nftVanBang = NftVanBang::where('token_id', $tokenId)
            ->whereIn('trang_thai', [NftVanBang::STATUS_SUCCESS, NftVanBang::STATUS_REVOKED])
            ->with([
                'nhanVienKy.chucVu', 
                'nhanVienKy.phongBan',
                'nhanVienKy.viNhanVien',
                'smartContract',
                'lichSuGiaoDichs' => function($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])
            ->first();

        if (!$nftVanBang) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy dữ liệu truy vết cho NFT này hoặc NFT chưa được đúc thành công.'
            ]);
        }

        $isRevoked = ($nftVanBang->trang_thai === NftVanBang::STATUS_REVOKED);
        $revocationInfo = null;

        if ($isRevoked) {
            $lichSuThuHoi = $nftVanBang->lichSuGiaoDichs
                ->whereIn('hanh_dong', ['THU_HOI_NFT', 'THU_HOI_NFT_BURN'])
                ->first();

            if ($lichSuThuHoi) {
                $revocationInfo = [
                    'reason'     => $lichSuThuHoi->loi_chi_tiet,
                    'revoked_at' => $lichSuThuHoi->created_at->format('Y-m-d H:i:s'),
                    'tx_hash'    => $lichSuThuHoi->transaction_hash,
                ];
            }
        }

        // Lấy thông tin hồ sơ gốc (Phân loại theo Polymorphic)
        $hoSoNguon = $nftVanBang->nftable;
        $sinhVien = $hoSoNguon ? $hoSoNguon->sinhVien : null;
        $nhanVien = $nftVanBang->nhanVienKy;

        // KIỂM TRA TÍNH TOÀN VẸN:
        // 1. Lấy dữ liệu On-chain từ Blockchain
        $onChainMetadataRaw = $blockchainService->layMetadataOnChain($tokenId);
        $onChainData = json_decode($onChainMetadataRaw, true);

        // 2. Lấy dữ liệu hiện tại từ Database
        $typeShort = '';
        if ($nftVanBang->nftable_type === BangDiem::class) $typeShort = 'bang_diem';
        elseif ($nftVanBang->nftable_type === ChungChi::class) $typeShort = 'chung_chi';
        elseif ($nftVanBang->nftable_type === DuAn::class) $typeShort = 'du_an';
        
        $offChainData = $hoSoNguon ? $this->layDuLieuHoSo($hoSoNguon, $typeShort) : null;

        // 3. Xây dựng danh sách đối soát chi tiết
        $isTampered = false;
        $detailedComparison = [];

        // So khớp trực tiếp dữ liệu thô (đã được băm và lưu trên chain)
        if ($onChainData && $offChainData) {
            foreach ($offChainData as $key => $offChainVal) {
                // Đảm bảo không lỗi nếu onChain thiếu trường
                $onChainVal = array_key_exists($key, $onChainData) ? $onChainData[$key] : 'N/A';
                
                // Ép kiểu về chuỗi để so sánh chính xác các số thập phân 0.0
                $onChainStr = is_null($onChainVal) ? 'null' : (string)$onChainVal;
                $offChainStr = is_null($offChainVal) ? 'null' : (string)$offChainVal;
                
                $match = ($onChainStr === $offChainStr);

                // Dịch tên trường sang Tiếng Việt tùy theo loại văn bằng
                $label = $this->dichNhauTruongDuLieu($typeShort, $key);

                // Định dạng hiển thị đẹp
                if (str_contains($key, 'ngay_')) {
                    if (strtotime($offChainStr)) $offChainStr = date('d/m/Y', strtotime($offChainStr));
                    if (strtotime($onChainStr)) $onChainStr = date('d/m/Y', strtotime($onChainStr));
                }

                $detailedComparison[] = [
                    'field'     => $label, 
                    'on_chain'  => $onChainStr,
                    'off_chain' => $offChainStr,
                    'match'     => $match
                ];

                if (!$match) $isTampered = true;
            }
        } else {
            $isTampered = true; // Nếu mất sạch onchain data
        }

        // Bằng chứng Blockchain (Lấy giao dịch đúc thành công đầu tiên)
        $giaoDichMint = $nftVanBang->lichSuGiaoDichs->where('hanh_dong', 'MINT_SUCCESS')->first();

        // Xác định Tên hồ sơ và Đơn vị cấp tùy theo loại
        $tenHoSo = 'Văn bằng chứng nhận';
        $donViCap = 'Trường Đại học Công nghệ';

        if ($typeShort === 'bang_diem' && $hoSoNguon) {
            $lopHoc = clone $hoSoNguon->lopHoc;
            $monHoc = $lopHoc ? $lopHoc->monHoc : null;
            $tenHoSo = "Bảng điểm " . ($monHoc ? $monHoc->ten_mon_hoc : '');
        } elseif ($typeShort === 'chung_chi' && $hoSoNguon) {
            $tenHoSo = $hoSoNguon->ten_chung_chi ?? 'Chứng chỉ';
            $donViCap = ($hoSoNguon->donViCap && $hoSoNguon->donViCap->ten_don_vi) 
                ? $hoSoNguon->donViCap->ten_don_vi 
                : ($hoSoNguon->ten_don_vi_cap_khac ?: $donViCap);
        } elseif ($typeShort === 'du_an' && $hoSoNguon) {
            $tenHoSo = $hoSoNguon->ten_du_an ?? 'Dự án';
        }

        return response()->json([
            'status' => true,
            'message' => 'Truy vết NFT thành công.',
            'data' => [
                'nft_info' => [
                    'token_id'   => $nftVanBang->token_id,
                    'token_uri'  => $nftVanBang->token_uri,
                    'data_hash'  => $nftVanBang->hash_du_lieu,
                    'ngay_ky'    => $nftVanBang->created_at->toIso8601String(),
                    'status'     => $isRevoked ? 'REVOKED' : 'ACTIVE',
                    'is_revoked' => $isRevoked,
                    'nftable_type' => class_basename($nftVanBang->nftable_type),
                    'ho_ten_sinh_vien' => $sinhVien ? $sinhVien->ho_ten : 'N/A',
                    'ten_ho_so'  => $tenHoSo,
                    'don_vi_cap' => $donViCap,
                ],
                'signer_info' => [
                    'name'       => $nhanVien ? $nhanVien->ho_ten : 'N/A',
                    'position'   => ($nhanVien && $nhanVien->chucVu) ? $nhanVien->chucVu->ten_chuc_vu : 'N/A',
                    'department' => ($nhanVien && $nhanVien->phongBan) ? $nhanVien->phongBan->ten_phong_ban : 'N/A',
                    'wallet'     => ($nhanVien && $nhanVien->viNhanVien) ? $nhanVien->viNhanVien->dia_chi_vi : 'N/A',
                ],
                'diploma_info' => [
                    'type'       => class_basename($nftVanBang->nftable_type),
                    'student'    => $sinhVien ? $sinhVien->ho_ten : 'N/A',
                ],
                'revocation_info' => $revocationInfo,
                'integrity' => [
                    'is_tampered'         => $isTampered,
                    'detailed_comparison' => $detailedComparison,
                ],
                'blockchain_proof' => [
                    'network'          => 'Sepolia Testnet',
                    'transaction_hash' => $nftVanBang->tx_hash_thanh_cong,
                    'block_number'     => $giaoDichMint ? $giaoDichMint->block_number : 'N/A',
                    'etherscan'        => 'https://sepolia.etherscan.io/tx/' . $nftVanBang->tx_hash_thanh_cong
                ]
            ]
        ]);
    }

    /**
     * ADMIN: Lấy danh sách tất cả NFT văn bằng để quản lý
     */
    public function danhSachNft(Request $request)
    {
        $limit = $request->input('limit', 10);
        
        $currentContract = SmartContract::where('dia_chi_contract', env('BLOCKCHAIN_CONTRACT_ADDRESS'))->first();
        $currentContractId = $currentContract ? $currentContract->id : 0;

        $query = NftVanBang::with(['nftable.sinhVien', 'nhanVienKy', 'lichSuGiaoDichs'])
                ->orderBy('updated_at', 'desc');

        // Lọc theo loại nếu cần
        if ($request->has('type') && $request->type != 'TatCa') {
            $typeClass = $request->type == 'BangDiem' ? 'App\\Models\\BangDiem' : ($request->type == 'ChungChi' ? 'App\\Models\\ChungChi' : 'App\\Models\\DuAn');
            $query->where('nftable_type', $typeClass);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status);
        }

        $list = $query->paginate($limit);

        // Biến đổi dữ liệu bổ sung thông tin Legacy và Burn Hash
        $list->getCollection()->transform(function($item) use ($currentContractId) {
            $item->is_legacy = ($item->smart_contract_id != $currentContractId);
            
            // Tìm tx_hash_burn từ lịch sử giao dịch (nếu đã thu hồi)
            $item->tx_hash_burn = null;
            if ($item->trang_thai === NftVanBang::STATUS_REVOKED) {
                $lichSuBurn = $item->lichSuGiaoDichs->where('hanh_dong', 'THU_HOI_NFT_BURN')->first();
                $item->tx_hash_burn = $lichSuBurn ? $lichSuBurn->transaction_hash : null;
            }
            
            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách NFT thành công.',
            'data' => $list
        ]);
    }

    private function timHoSoNguon($type, $id)
    {
        switch ($type) {
            case 'bang_diem': return BangDiem::find($id);
            case 'chung_chi': return ChungChi::find($id);
            case 'du_an':     return DuAn::find($id);
            default: return null;
        }
    }
    /**
     * ADMIN: Thu hồi một NFT đã cấp (do sai sót thông tin)
     */
    public function thuHoiNft(Request $request, BlockchainService $blockchainService)
    {
        $request->validate([
            'nft_van_bang_id' => 'required|integer|exists:nft_van_bangs,id',
            'reason'          => 'required|string|min:5',
        ]);

        DB::beginTransaction();
        try {
            $nftVanBang = NftVanBang::with('nftable')->findOrFail($request->nft_van_bang_id);

            // Kiểm tra: Chỉ thu hồi khi chưa bị thu hồi
            if ($nftVanBang->trang_thai === NftVanBang::STATUS_REVOKED) {
                 return response()->json(['status' => false, 'message' => 'NFT này đã được thu hồi trước đó.']);
            }

            // 1. Thực hiện Burn trên Blockchain (Nếu đã có Token ID)
            $txHashBurn = null;
            if ($nftVanBang->token_id !== null && $nftVanBang->trang_thai === NftVanBang::STATUS_SUCCESS) {
                try {
                    $ketQuaBurn = $blockchainService->caLenhBurnNft($nftVanBang->token_id);
                    $txHashBurn = $ketQuaBurn['transaction_hash'];
                } catch (\Exception $e) {
                    // Thông minh: Nếu là NFT cũ (Legacy)
                    Log::warning("Bỏ qua Burn On-chain: " . $e->getMessage());
                    // KHÔNG gán chuỗi cứng để tránh lỗi UNIQUE 'Duplicate entry' trong database
                    $txHashBurn = null; 
                }
            }

            // 2. Cập nhật trạng thái NFT thành REVOKED
            $nftVanBang->update([
                'trang_thai' => NftVanBang::STATUS_REVOKED
            ]);

            // 3. Mở khóa hồ sơ gốc và đặt lại trạng thái để cho phép sửa/đúc lại
            $record = $nftVanBang->nftable;
            if ($record) {
                $record->update([
                    'is_locked'  => false,
                    'trang_thai' => 0, // STATUS_NOT_MINTED
                ]);

                // 4. Thông báo cho sinh viên
                $loaiTen = $nftVanBang->nftable_type === BangDiem::class ? 'Bảng điểm' : ($nftVanBang->nftable_type === ChungChi::class ? 'Chứng chỉ' : 'Dự án');
                ThongBao::create([
                    'sinh_vien_id' => $record->sinh_vien_id,
                    'tieu_de'      => 'Thông báo: Thu hồi NFT Văn bằng',
                    'noi_dung'     => "Hồ sơ $loaiTen của bạn đã bị thu hồi (Burned) trên Blockchain bởi Admin. Lý do: " . $request->reason . ". Bạn hiện có thể chỉnh sửa lại hồ sơ.",
                    'loai'         => 'warning'
                ]);
            }

            // 5. Ghi lịch sử giao dịch (Lưu thêm tx_hash của lệnh burn nếu có)
            LichSuGiaoDich::create([
                'nft_van_bang_id'    => $nftVanBang->id,
                'nguoi_thuc_hien_id' => auth()->id(),
                'hanh_dong'          => 'THU_HOI_NFT_BURN',
                'transaction_hash'   => $txHashBurn,
                'loi_chi_tiet'       => 'Lý do thu hồi: ' . $request->reason,
                'trang_thai'         => 1 
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Đã tiêu hủy NFT trên Blockchain và thu hồi thành công trên hệ thống.',
                'tx_hash' => $txHashBurn
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi thu hồi NFT: " . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ]);
        }
    }
}
