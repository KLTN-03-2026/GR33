<template>
    <div class="chung-chi-management px-2">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title text-dark fw-800">Chứng chỉ của tôi</h3>
                <p class="page-subtitle text-muted">Hệ thống lưu trữ và xác thực văn bằng số.</p>
            </div>
            <button class="btn-pink shadow-sm" @click="moModalThem">
                <i class="bi bi-plus-circle-fill me-2"></i> Thêm chứng chỉ
            </button>
        </div>

        <!-- Main Content Card -->
        <div class="data-card shadow-sm border-0 bg-white" style="border-radius: 20px; overflow: hidden;">
            <div class="table-controls p-3 border-bottom d-flex justify-content-between align-items-center gap-3">
                <div class="navbar-search m-0" style="max-width: 400px; width: 100%;">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" v-model="tuKhoaTimKiem" placeholder="Tìm theo tên hoặc mã chứng chỉ..." />
                </div>
                <button class="btn btn-light-pink" @click="layDuLieu">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>

            <div class="table-responsive">
                <table class="flux-table text-nowrap">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">STT</th>
                            <th>Mã chứng chỉ</th>
                            <th>Tên chứng chỉ</th>
                            <th class="text-center">Phê duyệt</th>
                            <th class="text-center">NFT</th>
                            <th class="text-center">Yêu cầu NFT</th>
                            <th class="text-end" width="160">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="dangTai">
                            <td colspan="6" class="text-center py-5">
                                <div class="spinner-border text-accent" role="status"></div>
                            </td>
                        </tr>
                        <tr v-else-if="danhSachLoc.length === 0">
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state py-4">
                                    <i class="bi bi-journal-bookmark d-block mb-2 opacity-25" style="font-size: 3rem"></i>
                                    <span class="text-muted">Bạn chưa có chứng chỉ nào được ghi nhận.</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else v-for="(item, index) in danhSachLoc" :key="item.id">
                            <td class="text-center text-muted fw-600">#{{ index + 1 }}</td>
                            <td class="fw-700 text-accent">{{ item.ma_chung_chi }}</td>
                            <td>
                                <div class="fw-700 text-main">{{ item.ten_chung_chi }}</div>
                                <div class="small text-muted">{{ layTenDonVi(item) }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge shadow-sm" :class="layLopPheDuyet(item.is_phe_duyet)">
                                    {{ layTenPheDuyet(item.is_phe_duyet) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge" :class="layLopTrangThai(item.trang_thai)">
                                    {{ layTenTrangThai(item.trang_thai) }}
                                </span>
                                <div v-if="item.trang_thai === 1 && item.nft_van_bang?.tx_hash_thanh_cong" class="mt-1 text-center">
                                    <a :href="'https://sepolia.etherscan.io/tx/' + item.nft_van_bang.tx_hash_thanh_cong" 
                                       target="_blank" class="text-accent small text-decoration-none fw-700">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Etherscan
                                    </a>
                                </div>
                                <div v-else-if="item.trang_thai === 4" class="mt-1 text-center">
                                    <a v-if="item.nft_van_bang?.tx_hash_burn" 
                                       :href="'https://sepolia.etherscan.io/tx/' + item.nft_van_bang.tx_hash_burn" 
                                       target="_blank" class="text-danger small text-decoration-none fw-700">
                                        <i class="bi bi-fire me-1"></i>Bằng chứng Hủy
                                    </a>
                                    <span v-else class="text-muted small fw-600">
                                        <i class="bi bi-slash-circle me-1"></i>Đã thu hồi (Legacy)
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2" style="min-height: 40px">
                                    <button v-if="item.trang_thai == 0 && item.is_phe_duyet == 1" 
                                        class="btn btn-sm btn-pink-outline rounded-pill fw-700 px-3"
                                        @click="guiYeuCauNFT(item)"
                                        :disabled="dangLuuNFT">
                                        <i class="bi bi-send-check-fill me-1"></i> Gửi yêu cầu
                                    </button>
                                    <span v-else-if="item.trang_thai == 2" class="text-muted small fw-600">
                                        <i class="bi bi-hourglass-split me-1"></i> Chờ duyệt
                                    </span>
                                    <span v-else-if="item.trang_thai == 1" class="text-success small fw-700">
                                        <i class="bi bi-patch-check-fill me-1"></i> Đã cấp
                                    </span>
                                    <span v-else class="text-muted small italic">
                                        Chờ phê duyệt hồ sơ
                                    </span>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">

                                    <button class="btn btn-action-pink" @click="moModalChiTiet(item)" title="Xem chi tiết">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                    <button class="btn btn-action-pink" @click="moModalSua(item)" :disabled="item.trang_thai == 1">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Thêm/Sửa -->
        <div class="modal fade" id="chungChiModal" tabindex="-1" aria-hidden="true" ref="refsModal">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
                    <div class="modal-header border-0 bg-pink-v2 text-white p-4">
                        <h5 class="modal-title fw-800">{{ laCapNhat ? 'Cập nhật Chứng chỉ' : 'Đăng ký Chứng chỉ mới' }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form @submit.prevent="xuLyLuu">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Mã chứng chỉ</label>
                                    <input type="text" class="form-control flux-input-lg" v-model="duLieuForm.ma_chung_chi"
                                        placeholder="VD: CC123456" :disabled="laCapNhat" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Tên chứng chỉ</label>
                                    <input type="text" class="form-control flux-input-lg" v-model="duLieuForm.ten_chung_chi"
                                        placeholder="Nhập tên chứng chỉ" required>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-700 small text-uppercase opacity-75 m-0">Đơn vị cấp</label>
                                        <div class="form-check form-switch p-0 m-0 d-flex align-items-center gap-2">
                                            <label class="small fw-600 text-muted m-0" for="switchDonVi">Đơn vị ngoài</label>
                                            <input class="form-check-input m-0 cursor-pointer" type="checkbox" id="switchDonVi" v-model="isDonViKhac">
                                        </div>
                                    </div>
                                    
                                    <div v-if="!isDonViKhac">
                                        <select class="form-select flux-input-lg" v-model="duLieuForm.don_vi_cap_id" required>
                                            <option value="" disabled>Chọn đơn vị cấp...</option>
                                            <option v-for="dv in danhSachDonViCap" :key="dv.id" :value="dv.id">
                                                {{ dv.ten_don_vi }}
                                            </option>
                                        </select>
                                    </div>
                                    <div v-else>
                                        <input type="text" class="form-control flux-input-lg" v-model="duLieuForm.ten_don_vi_cap_khac" 
                                            placeholder="Nhập tên đơn vị ngoài (VD: British Council)" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Loại chứng chỉ</label>
                                    <select class="form-select flux-input-lg" v-model="duLieuForm.loai_chung_chi" required>
                                        <option value="ngoai_ngu">Ngoại ngữ</option>
                                        <option value="tin_hoc">Tin học</option>
                                        <option value="ky_nang">Kỹ năng</option>
                                        <option value="bang_cap">Bằng cấp</option>
                                        <option value="khac">Khác</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Điểm số</label>
                                    <input type="text" class="form-control flux-input-lg"
                                        v-model="duLieuForm.diem_so" placeholder="VD: 8.0, 900">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Xếp loại</label>
                                    <input type="text" class="form-control flux-input-lg" v-model="duLieuForm.xep_loai" placeholder="Khá, Giỏi">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Ngày cấp</label>
                                    <input type="date" class="form-control flux-input-lg" v-model="duLieuForm.ngay_cap" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Ngày hết hạn</label>
                                    <input type="date" class="form-control flux-input-lg" v-model="duLieuForm.ngay_het_han">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Bản scan chứng chỉ (PDF/Ảnh)</label>
                                    <div class="file-upload-wrapper border rounded-4 p-4 text-center"
                                        @click="$refs.fileInput.click()"
                                        style="border-style: dashed !important; border-width: 2px !important; background: #fdfdfd; cursor: pointer; transition: all 0.3s;">
                                        <input type="file" ref="fileInput" class="d-none" @change="chonFile" accept=".pdf,.jpg,.jpeg,.png">
                                        <div v-if="!fileChon && !duLieuForm.file_dinh_kem">
                                            <i class="bi bi-cloud-upload fs-1 text-muted opacity-50"></i>
                                            <div class="mt-2 fw-600 text-muted">Nhấp vào đây để tải lên bản scan</div>
                                            <div class="small text-muted opacity-75">Hỗ trợ PDF, JPG, PNG (Tối đa 2MB)</div>
                                        </div>
                                        <div v-else>
                                            <i class="bi bi-file-earmark-check fs-1 text-success"></i>
                                            <div class="mt-2 fw-700 text-main">{{ fileChon ? fileChon.name : 'Đã có file đính kèm' }}</div>
                                            <div class="small text-muted mt-1" v-if="duLieuForm.file_dinh_kem && !fileChon">
                                                <a :href="duLieuForm.file_dinh_kem" target="_blank" @click.stop class="text-accent text-decoration-none">
                                                    Xem file hiện tại <i class="bi bi-box-arrow-up-right small"></i>
                                                </a>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" @click.stop="fileChon = null; duLieuForm.file_dinh_kem = null">
                                                Thay đổi file khác
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-2 d-flex gap-3">
                                <button type="button" class="btn btn-light-pink px-4 flex-fill fw-600"
                                    data-bs-dismiss="modal">Hủy bỏ</button>
                                <button type="submit" class="btn btn-pink-v2 px-4 flex-fill fw-800 shadow-pink"
                                    :disabled="dangLuu">
                                    <span v-if="dangLuu" class="spinner-border spinner-border-sm me-1"></span>
                                    {{ laCapNhat ? 'Cập nhật' : 'Gửi yêu cầu' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Chi tiết -->
        <div class="modal fade" id="chiTietModal" tabindex="-1" aria-hidden="true" ref="refsModalChiTiet">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
                    <div class="modal-header border-0 bg-pink-v2 text-white p-4">
                        <h5 class="modal-title fw-800">Thông tin văn bằng số</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4" v-if="itemDuocChon">
                        <div class="row g-4">
                            <div class="col-md-5">
                                <div class="detail-group">
                                    <label class="small text-uppercase fw-800 text-muted opacity-50">Minh chứng</label>
                                    <div class="mt-2 text-center p-2 border rounded-4 bg-light">
                                        <img v-if="itemDuocChon.file_dinh_kem" :src="itemDuocChon.file_dinh_kem" 
                                            class="img-fluid rounded-3 shadow-sm" alt="Evidence" 
                                            style="max-height: 250px; cursor: pointer" 
                                            @click="window.open(itemDuocChon.file_dinh_kem, '_blank')">
                                        <div v-else class="py-5 text-muted opacity-50">
                                            <i class="bi bi-image-fill display-4"></i>
                                            <div class="small mt-1">Chưa có minh chứng ảnh</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="small text-uppercase fw-800 text-muted opacity-50">Tên chứng chỉ</label>
                                        <div class="fw-800 fs-5 text-accent">{{ itemDuocChon.ten_chung_chi }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-800 text-muted opacity-50">Mã chứng chỉ</label>
                                        <div class="fw-700 text-dark">{{ itemDuocChon.ma_chung_chi }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-800 text-muted opacity-50">Loại</label>
                                        <div class="fw-700 text-dark">{{ itemDuocChon.loai_chung_chi }}</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="small text-uppercase fw-800 text-muted opacity-50">Đơn vị cấp</label>
                                        <div class="fw-700 text-dark">{{ layTenDonVi(itemDuocChon) }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-800 text-muted opacity-50">Kết quả</label>
                                        <div class="fw-700 text-dark">{{ itemDuocChon.diem_so || '---' }} {{ itemDuocChon.xep_loai ? '(' + itemDuocChon.xep_loai + ')' : '' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-800 text-muted opacity-50">Ngày cấp</label>
                                        <div class="fw-700 text-dark">{{ itemDuocChon.ngay_cap }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-800 text-muted opacity-50">Ngày hết hạn</label>
                                        <div class="fw-700 text-dark">{{ itemDuocChon.ngay_het_han || 'Vĩnh viễn' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-uppercase fw-800 text-muted opacity-50">Trạng thái</label>
                                        <div class="mt-1">
                                            <span class="badge" :class="layLopTrangThai(itemDuocChon.trang_thai)">
                                                {{ layTenTrangThai(itemDuocChon.trang_thai) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light border px-4 fw-600 rounded-3"
                            data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Xác nhận NFT -->
        <div class="modal fade" id="confirmNftModal" tabindex="-1" aria-hidden="true" ref="refsModalNFT">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                    <div class="modal-header border-0 bg-pink-gradient text-white p-4" style="border-radius: 24px 24px 0 0">
                        <h5 class="modal-title fw-800">Xác nhận yêu cầu NFT</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="mb-4">
                            <i class="bi bi-shield-lock-fill text-accent display-1 opacity-25"></i>
                        </div>
                        <h5 class="fw-800 text-dark mb-3">Bạn chắc chắn muốn tiếp tục?</h5>
                        <p class="text-muted mb-0">Hồ sơ sau khi gửi yêu cầu sẽ được <span class="text-accent fw-700">KHÓA TUYỆT ĐỐI</span> để đảm bảo tính toàn vẹn của dữ liệu trong suốt quá trình đúc NFT trên Blockchain.</p>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light-pink flex-fill py-2 fw-600" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn btn-pink flex-fill py-2 fw-800 shadow-pink" 
                            @click="thucHienGuiYeuCau" :disabled="dangLuuNFT">
                            <span v-if="dangLuuNFT" class="spinner-border spinner-border-sm me-2"></span>
                            Xác nhận gửi
                        </button>
                    </div>
                </div>
            </div>
        </div>


    </div>
</template>

<script>
import baseRequestSinhVien from "../../../core/baseRequestSinhVien";

export default {
    name: "SinhVienChungChi",
    data() {
        return {
            danhSach: [],
            danhSachDonViCap: [],
            dangTai: false,
            dangLuu: false,
            dangLuuNFT: false,
            laCapNhat: false,
            tuKhoaTimKiem: "",
            isDonViKhac: false,
            fileChon: null,
            duLieuForm: {
                id: null,
                ma_chung_chi: "",
                ten_chung_chi: "",
                loai_chung_chi: "ngoai_ngu",
                don_vi_cap_id: "",
                ten_don_vi_cap_khac: "",
                ngay_cap: "",
                ngay_het_han: "",
                diem_so: "",
                xep_loai: "",
                file_dinh_kem: null
            },
            itemDuocChon: null,
            itemYeuCau: null,
            instanceModal: null,
            instanceModalChiTiet: null,
            instanceModalNFT: null,
        };
    },
    computed: {
        danhSachLoc() {
            if (!this.tuKhoaTimKiem) return this.danhSach;
            const kw = this.tuKhoaTimKiem.toLowerCase();
            return this.danhSach.filter(item => 
                item.ten_chung_chi.toLowerCase().includes(kw) || 
                item.ma_chung_chi.toLowerCase().includes(kw)
            );
        }
    },
    mounted() {
        this.layDuLieu();
        this.layDonViCap();
        if (window.bootstrap) {
            this.instanceModal = new window.bootstrap.Modal(this.$refs.refsModal);
            this.instanceModalChiTiet = new window.bootstrap.Modal(this.$refs.refsModalChiTiet);
            this.instanceModalNFT = new window.bootstrap.Modal(this.$refs.refsModalNFT);
        }
    },
    methods: {
        layTenTrangThai(trangThai) {
            if (trangThai == 1) return 'Đã Đúc NFT';
            if (trangThai == 2) return 'Chờ Duyệt';
            if (trangThai == 4) return 'Đã Thu Hồi';
            return 'Chưa Đúc';
        },
        layTenPheDuyet(status) {
            if (status == 1) return 'Đã Duyệt';
            if (status == 2) return 'Từ Chối';
            return 'Chờ Duyệt';
        },
        layLopPheDuyet(status) {
            if (status == 1) return 'bg-success text-white px-3';
            if (status == 2) return 'bg-danger text-white px-3';
            return 'bg-warning text-dark px-3';
        },
        layLopTrangThai(trangThai) {
            if (trangThai == 1) return 'bg-success-subtle text-success border border-success';
            if (trangThai == 2) return 'bg-warning-subtle text-warning border border-warning';
            if (trangThai == 4) return 'bg-danger-subtle text-danger border border-danger fw-800';
            return 'bg-secondary-subtle text-secondary border border-secondary';
        },
        layTenDonVi(item) {
            if (item.don_vi_cap_id) {
                const unit = this.danhSachDonViCap.find(u => u.id == item.don_vi_cap_id);
                return unit ? unit.ten_don_vi : (item.don_vi_cap?.ten_don_vi || '---');
            }
            return item.ten_don_vi_cap_khac || '---';
        },
        layDuLieu() {
            this.dangTai = true;
            // Đồng bộ profile để cập nhật localStorage
            baseRequestSinhVien.get("profile")
                .then(res => {
                    if (res.data.status) {
                        localStorage.setItem("sinh_vien_user", JSON.stringify(res.data.data));
                    }
                });

            baseRequestSinhVien.get("chung-chis/get-data")
                .then(res => {
                    this.danhSach = res.data.data || [];
                })
                .catch(err => {
                    this.$toast.error("Lỗi tải danh sách chứng chỉ!");
                })
                .finally(() => {
                    this.dangTai = false;
                });
        },
        layDonViCap() {
            baseRequestSinhVien.get("don-vi-caps/all")
                .then(res => {
                    this.danhSachDonViCap = res.data.data || [];
                });
        },
        chonFile(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    this.$toast.error("Lưu ý: File không được vượt quá 2MB");
                    return;
                }
                this.fileChon = file;
            }
        },
        moModalThem() {
            this.laCapNhat = false;
            this.isDonViKhac = false;
            this.fileChon = null;
            this.duLieuForm = { 
                id: null, ma_chung_chi: "", ten_chung_chi: "", loai_chung_chi: "ngoai_ngu", 
                don_vi_cap_id: "", ten_don_vi_cap_khac: "", ngay_cap: "", ngay_het_han: "", 
                diem_so: "", xep_loai: "" 
            };
            this.instanceModal.show();
        },
        moModalSua(item) {
            this.laCapNhat = true;
            this.isDonViKhac = !!item.ten_don_vi_cap_khac;
            this.fileChon = null;
            this.duLieuForm = { ...item };
            this.instanceModal.show();
        },
        moModalChiTiet(item) {
            this.itemDuocChon = item;
            this.instanceModalChiTiet.show();
        },
        xuLyLuu() {
            this.dangLuu = true;
            const formData = new FormData();
            
            Object.keys(this.duLieuForm).forEach(key => {
                if (key !== 'file_dinh_kem' && this.duLieuForm[key] !== null && this.duLieuForm[key] !== undefined) {
                    formData.append(key, this.duLieuForm[key]);
                }
            });

            if (this.isDonViKhac) {
                formData.set('don_vi_cap_id', '');
            } else {
                formData.set('ten_don_vi_cap_khac', '');
            }

            if (this.fileChon) {
                formData.append('file_dinh_kem', this.fileChon);
            }

            const url = this.laCapNhat 
                ? `chung-chis/update/${this.duLieuForm.id}`
                : `chung-chis/create`;

            if (this.laCapNhat) {
                formData.append('_method', 'PUT');
            }

            // Lưu ý: Laravel Route::post cho cả update khi gửi file qua FormData
            baseRequestSinhVien.post(url, formData)
                .then(res => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.instanceModal.hide();
                        this.layDuLieu();
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch(err => {
                    const errors = err.response?.data?.errors;
                    if (errors) Object.values(errors).forEach(e => this.$toast.error(e[0]));
                    else this.$toast.error("Lỗi gửi yêu cầu xác thực!");
                })
                .finally(() => {
                    this.dangLuu = false;
                });
        },
        guiYeuCauNFT(item) {
            const userStr = localStorage.getItem("sinh_vien_user");
            const user = userStr ? JSON.parse(userStr) : null;
            
            // Kiểm tra linh hoạt cả 2 định dạng (snake_case và camelCase) của Laravel
            const vi = user?.vi_sinh_vien || user?.viSinhVien;

            if (!vi?.dia_chi_vi) {
                this.$toast.error("Tài khoản của bạn chưa được liên kết với ví Blockchain. Vui lòng cài đặt ví tại thông tin cá nhân trước khi thực hiện yêu cầu NFT.");
                return;
            }

            this.itemYeuCau = item;
            this.instanceModalNFT.show();
        },
        thucHienGuiYeuCau() {
            if (!this.itemYeuCau) return;
            
            this.dangLuuNFT = true;
            baseRequestSinhVien.post("nft/request", {
                record_id: this.itemYeuCau.id,
                type: 'chung_chi'
            }).then(res => {
                if (res.data.status) {
                    this.$toast.success(res.data.message);
                    this.instanceModalNFT.hide();
                    this.layDuLieu();
                } else {
                    this.$toast.error(res.data.message);
                }
            }).finally(() => {
                this.dangLuuNFT = false;
            });
        },

    }
};
</script>

<style scoped>
.btn-pink { background: #db2777; color: white; border-radius: 14px; padding: 10px 20px; border: none; font-weight: 700; transition: all 0.3s; }
.btn-pink:hover { background: #be185d; transform: translateY(-2px); }
.btn-light-pink { background: #fdf2f8; color: #db2777; border-radius: 12px; padding: 8px 16px; border: 1px solid #fce7f3; font-weight: 600; }
.bg-pink-gradient { background: linear-gradient(135deg, #db2777 0%, #be185d 100%); }
.bg-pink-light { background: #fdf2f8; }
.border-pink { border-color: #fce7f3 !important; }
.btn-action-pink { width: 36px; height: 36px; border-radius: 10px; background: #fff; border: 1px solid #fce7f3; color: #db2777; display: flex; align-items: center; justify-content: center; }
.btn-pink-outline {
    color: #db2777; border: 1.5px solid #db2777; transition: all 0.2s;
}
.btn-pink-outline:hover {
    background: #db2777; color: white;
}
.flux-input-lg { padding: 12px 16px; border-radius: 14px; border: 1px solid #e2e8f0; font-size: 14px; }
.flux-input-lg:focus { border-color: #db2777; box-shadow: 0 0 0 4px rgba(219,39,119,0.1); outline: none; }
.btn-pink-v2 {
    background: #db2777;
    color: #fff;
    border: none;
}
.btn-pink-v2:hover {
    background: #be185d;
    color: #fff;
}
.bg-pink-v2 {
    background: linear-gradient(135deg, #db2777, #be185d);
}
.file-upload-wrapper:hover {
    border-color: #db2777 !important;
    background: #fff5f7 !important;
}
.detail-group {
    padding: 15px;
    background: #fff9fb;
    border-radius: 16px;
    border: 1px solid #fce7f3;
    height: 100%;
}
.text-main { color: #1e293b; }
.fw-800 { font-weight: 800; }
</style>
