<template>
    <div class="chung-chi-management">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title">Quản lý Chứng Chỉ</h3>
                <p class="page-subtitle">Hệ thống quản lý danh mục chứng chỉ và đúc NFT.</p>
            </div>
            <button v-if="$hasPermission(42)" class="btn-new" @click="moModalThem">
                <i class="bi bi-plus-circle-fill"></i> Thêm chứng chỉ
            </button>
        </div>

        <!-- Main Content Card -->
        <div class="data-card shadow-sm border-0">
            <!-- Table Controls -->
            <div
                class="table-controls p-3 border-bottom bg-light-subtle d-flex justify-content-between align-items-center gap-3">
                <div class="navbar-search m-0" style="max-width: 400px; width: 100%;">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" v-model="tuKhoaTimKiem" placeholder="Tìm theo tên SV, mã SV hoặc tên CC..." />
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select btn-light border fw-600 me-2" style="width: auto;" v-model="kieuSapXep">
                        <option value="newest">Mới nhất</option>
                        <option value="oldest">Cũ nhất</option>
                    </select>
                    <button class="btn btn-light border" @click="layDuLieu">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>

            <!-- Table Content -->
            <div class="table-responsive">
                <table class="flux-table text-nowrap">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">STT</th>
                            <th>Mã CC</th>
                            <th>Tên chứng chỉ</th>
                            <th>Sinh viên</th>
                            <th>Đơn vị cấp</th>
                            <th>Ngày cấp</th>
                            <th class="text-center">NFT</th>
                            <th class="text-center">Khóa</th>
                            <th class="text-end" width="160">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="dangTai">
                            <td colspan="9" class="text-center py-5">
                                <div class="spinner-border text-rose" role="status"></div>
                            </td>
                        </tr>
                        <tr v-else-if="danhSachLoc.length === 0">
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-journal-bookmark d-block mb-2 opacity-25"
                                        style="font-size: 3rem"></i>
                                    <span class="text-muted">Không tìm thấy chứng chỉ nào.</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else v-for="(item, index) in danhSachPhanTrang" :key="item.id">
                            <td class="text-center text-muted fw-600">#{{ (trangHienTai - 1) * soBanGhiTrenTrang + index + 1
                            }}</td>
                            <td class="fw-700 text-rose-dark">{{ item.ma_chung_chi }}</td>
                            <td>
                                <div class="fw-700 text-main">{{ item.ten_chung_chi }}</div>
                                <div class="small text-muted">{{ getTenLoaiChungChi(item.loai_chung_chi) }}</div>
                            </td>
                            <td>
                                <div class="fw-600">{{ item.sinh_vien?.ho_ten || '---' }}</div>
                                <div class="small text-muted">{{ item.sinh_vien?.ma_sinh_vien }}</div>
                            </td>
                            <td>{{ layTenDonVi(item) }}</td>
                            <td>{{ item.ngay_cap }}</td>
                            <td class="text-center">
                                <span class="badge" :class="layLopTrangThai(item.trang_thai)">
                                    {{ layTenTrangThai(item.trang_thai) }}
                                </span>
                                <div v-if="item.trang_thai === 1 && item.nft_van_bang?.tx_hash_thanh_cong" class="mt-1">
                                    <a :href="'https://sepolia.etherscan.io/tx/' + item.nft_van_bang.tx_hash_thanh_cong" 
                                       target="_blank" class="text-info small text-decoration-none fw-600">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Etherscan
                                    </a>
                                </div>
                                <div v-if="item.trang_thai === 3 && item.nft_van_bang?.trang_thai === 4" class="mt-1">
                                    <a v-if="item.nft_van_bang?.tx_hash_burn" 
                                       :href="'https://sepolia.etherscan.io/tx/' + item.nft_van_bang.tx_hash_burn" 
                                       target="_blank" class="text-danger small text-decoration-none fw-600">
                                        <i class="bi bi-fire me-1"></i>Bằng chứng Hủy
                                    </a>
                                    <span v-else class="text-muted small fw-600">
                                        <i class="bi bi-slash-circle me-1"></i>Đã thu hồi (Legacy)
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <i v-if="item.is_locked" class="bi bi-lock-fill text-danger" title="Đã khóa"></i>
                                <i v-else class="bi bi-unlock-fill text-success" title="Không khóa"></i>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">

                                    <button class="btn btn-action shadow-sm" @click="moModalChiTiet(item)" title="Xem chi tiết">
                                        <i class="bi bi-eye-fill text-info"></i>
                                    </button>
                                    <button v-if="!item.is_locked && $hasPermission(45)" class="btn btn-action shadow-sm" @click="moModalSua(item)" title="Sửa">
                                        <i class="bi bi-pencil-fill text-primary-darker"></i>
                                    </button>
                                    <button v-else-if="item.is_locked" class="btn btn-action shadow-sm opacity-50 cursor-not-allowed" disabled title="Hồ sơ đang bị khóa để xử lý Blockchain">
                                        <i class="bi bi-pencil-fill text-muted"></i>
                                    </button>
                                    <button v-if="item.trang_thai !== 1 && !item.is_locked && $hasPermission(46)" class="btn btn-action shadow-sm" @click="xuLyXoa(item)" title="Xóa chứng chỉ">
                                        <i class="bi bi-trash-fill text-danger"></i>
                                    </button>
                                    <button v-else-if="item.trang_thai === 1 || item.is_locked" class="btn btn-action shadow-sm opacity-50 cursor-not-allowed" disabled title="Không thể xóa hồ sơ đã đúc NFT hoặc đã khóa">
                                        <i class="bi bi-trash-fill text-muted"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="table-footer d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top mt-1"
                v-if="tongSoTrang > 1">
                <span class="small text-muted mb-3 mb-md-0">
                    Hiển thị <b>{{ danhSachPhanTrang.length > 0 ? (trangHienTai - 1) * soBanGhiTrenTrang + 1 : 0 }}</b>
                    - <b>{{ Math.min(trangHienTai * soBanGhiTrenTrang, danhSachLoc.length) }}</b>
                    trong tổng số <b>{{ danhSachLoc.length }}</b> chứng chỉ
                </span>
                <nav>
                    <ul class="pagination pagination-sm m-0 gap-1">
                        <li class="page-item" :class="{ disabled: trangHienTai === 1 }">
                            <a class="page-link border-0 rounded-circle shadow-sm" href="#"
                                @click.prevent="trangHienTai--">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item" v-for="p in cacTrangHienThi" :key="p" :class="{ active: trangHienTai === p }">
                            <a class="page-link border-0 rounded-circle shadow-sm" href="#"
                                @click.prevent="trangHienTai = p">{{ p
                                }}</a>
                        </li>
                        <li class="page-item" :class="{ disabled: trangHienTai === tongSoTrang }">
                            <a class="page-link border-0 rounded-circle shadow-sm" href="#"
                                @click.prevent="trangHienTai++">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Edit/Create Modal -->
        <div class="modal fade" id="chungChiModal" tabindex="-1" aria-hidden="true" ref="refsModal">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
                    <div class="modal-header border-0 bg-rose-v2 text-white p-4">
                        <h5 class="modal-title fw-800">{{ laCapNhat ? 'Cập nhật Chứng chỉ' : 'Tạo Chứng chỉ mới' }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form @submit.prevent="xuLyLuu">
                            <div class="row g-3">
                                <div class="col-md-6" v-if="laCapNhat">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Mã chứng chỉ</label>
                                    <input type="text" class="form-control flux-input bg-light" v-model="duLieuForm.ma_chung_chi"
                                        disabled placeholder="Sẽ được tạo tự động">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Tên chứng
                                        chỉ</label>
                                    <input type="text" class="form-control flux-input" v-model="duLieuForm.ten_chung_chi"
                                        placeholder="Nhập tên chứng chỉ">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Sinh viên</label>
                                    <div class="navbar-search mb-2 w-100" style="max-width: 100%;">
                                        <i class="bi bi-search search-icon"></i>
                                        <input type="text" v-model="tuKhoaTimKiemSinhVien" placeholder="Tìm theo tên hoặc mã SV..." />
                                    </div>
                                    <select class="form-select flux-input" v-model="duLieuForm.sinh_vien_id" required>
                                        <option value="" disabled>Chọn sinh viên...</option>
                                        <option v-for="sv in danhSachSinhVienLoc" :key="sv.id" :value="sv.id">
                                            {{ sv.ma_sinh_vien }} - {{ sv.ho_ten }}
                                        </option>
                                    </select>
                                    <div class="small text-muted mt-1" v-if="tuKhoaTimKiemSinhVien && danhSachSinhVienLoc.length === 0">
                                        Không tìm thấy sinh viên nào khớp.
                                    </div>
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
                                        <select class="form-select flux-input" v-model="duLieuForm.don_vi_cap_id" required>
                                            <option value="" disabled>Chọn đơn vị cấp...</option>
                                            <option v-for="dv in danhSachDonViCap" :key="dv.id" :value="dv.id">
                                                {{ dv.ten_don_vi }}
                                            </option>
                                        </select>
                                    </div>
                                    <div v-else>
                                        <input type="text" class="form-control flux-input" v-model="duLieuForm.ten_don_vi_cap_khac" 
                                            placeholder="Nhập tên đơn vị ngoài (VD: British Council)" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Loại chứng chỉ</label>
                                    <select class="form-select flux-input" v-model="duLieuForm.loai_chung_chi" required>
                                        <option value="ngoai_ngu">Ngoại ngữ</option>
                                        <option value="tin_hoc">Tin học</option>
                                        <option value="ky_nang">Kỹ năng</option>
                                        <option value="bang_cap">Bằng cấp</option>
                                        <option value="khac">Khác</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Điểm số</label>
                                    <input type="text" class="form-control flux-input"
                                        v-model="duLieuForm.diem_so" placeholder="VD: 8.0, 900, (Nếu có)">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Xếp loại</label>
                                    <input type="text" class="form-control flux-input" v-model="duLieuForm.xep_loai" placeholder="Khá, Giỏi, (Nếu có)">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Ngày cấp</label>
                                    <input type="date" class="form-control flux-input" v-model="duLieuForm.ngay_cap">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Ngày hết
                                        hạn</label>
                                    <input type="date" class="form-control flux-input" v-model="duLieuForm.ngay_het_han">
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
                                                <a :href="layFullUrl(duLieuForm.file_dinh_kem)" target="_blank" @click.stop class="text-primary text-decoration-none">
                                                    Xem file hiện tại <i class="bi bi-box-arrow-up-right small"></i>
                                                </a>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" @click.stop="fileChon = null; duLieuForm.file_dinh_kem = null">
                                                Thay đổi file khác
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Status fields removed as per request -->
                            </div>

                            <div class="mt-4 pt-2 d-flex gap-3">
                                <button type="button" class="btn btn-light border px-4 flex-fill fw-600"
                                    data-bs-dismiss="modal">Hủy bỏ</button>
                                <button type="submit" class="btn btn-rose-v2 px-4 flex-fill fw-700 shadow-sm"
                                    :disabled="dangLuu">
                                    <span v-if="dangLuu" class="spinner-border spinner-border-sm me-1"></span>
                                    {{ laCapNhat ? 'Cập nhật' : 'Thêm mới' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="detailChungChiModal" tabindex="-1" aria-hidden="true" ref="refsModalChiTiet">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
                    <div class="modal-header border-0 bg-rose-v2 text-white p-4">
                        <h5 class="modal-title fw-800">Chi tiết Chứng chỉ</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4" v-if="itemDuocChon">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="small text-uppercase fw-800 text-muted opacity-50">Thông tin chứng
                                        chỉ</label>
                                    <div class="mt-2 text-dark">
                                        <div class="fw-800 fs-5 text-rose-dark">{{ itemDuocChon.ten_chung_chi }}</div>
                                        <div class="fw-600">Mã: {{ itemDuocChon.ma_chung_chi }}</div>
                                        <div>Loại: {{ itemDuocChon.loai_chung_chi }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="small text-uppercase fw-800 text-muted opacity-50">Sinh viên thụ
                                        hưởng</label>
                                    <div class="mt-2">
                                        <div class="fw-800">{{ itemDuocChon.sinh_vien?.ho_ten }}</div>
                                        <div class="text-muted">MSSV: {{ itemDuocChon.sinh_vien?.ma_sinh_vien }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="small text-uppercase fw-800 text-muted opacity-50">Đơn vị & Kết
                                        quả</label>
                                    <div class="mt-2">
                                        <div>Đơn vị cấp: {{ layTenDonVi(itemDuocChon) }}</div>
                                        <div>Điểm số: <span class="fw-700 text-rose-dark">{{ itemDuocChon.diem_so || 'Không có'
                                                }}</span>
                                        </div>
                                        <div>Xếp loại: <span class="badge bg-rose-subtle text-rose-dark border">{{
                                            itemDuocChon.xep_loai || 'Không có' }}</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="small text-uppercase fw-800 text-muted opacity-50">Thời hạn & Trạng
                                        thái</label>
                                    <div class="mt-2">
                                        <div>Ngày cấp: {{ itemDuocChon.ngay_cap }}</div>
                                        <div>Hết hạn: {{ itemDuocChon.ngay_het_han || 'Vĩnh viễn' }}</div>
                                        <div class="mt-2 d-flex gap-2">
                                            <span class="badge" :class="layLopTrangThai(itemDuocChon.trang_thai)">{{
                                                layTenTrangThai(itemDuocChon.trang_thai) }}</span>
                                            <span v-if="itemDuocChon.is_locked"
                                                class="badge bg-danger-subtle text-danger border border-danger"><i
                                                    class="bi bi-lock-fill"></i> Đã khóa</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light border px-4 fw-600 rounded-3"
                            data-bs-dismiss="modal">Đóng cửa sổ</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteChungChiModal" tabindex="-1" aria-hidden="true" ref="modalElementXoa">
            <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 400px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3">
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="fw-800 text-dark mb-2">Xác nhận xóa?</h4>
                        <p class="text-muted mb-4">Bạn có chắc chắn muốn xóa chứng chỉ <b>{{ itemXoa?.ten_chung_chi }}</b> của sinh viên <b>{{ itemXoa?.sinh_vien?.ho_ten }}</b>?</p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light border flex-fill fw-600" data-bs-dismiss="modal">Hủy bỏ</button>
                            <button type="button" class="btn btn-danger flex-fill fw-700" @click="xacNhanXoa" :disabled="dangLuu">
                                <span v-if="dangLuu" class="spinner-border spinner-border-sm me-1"></span>
                                Xác nhận xóa CC
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</template>

<script>
import baseRequestAdmin from "../../../core/baseRequestAdmin";

export default {
    name: "QuanLyChungChi",
    data() {
        return {
            danhSach: [],
            danhSachSinhVien: [],
            danhSachDonViCap: [],
            dangTai: false,
            dangLuu: false,
            laCapNhat: false,
            tuKhoaTimKiem: "",
            kieuSapXep: 'newest',
            tuKhoaTimKiemSinhVien: "",
            duLieuForm: {
                id: null,
                ma_chung_chi: "",
                ten_chung_chi: "",
                sinh_vien_id: "",
                don_vi_cap_id: "",
                ten_don_vi_cap_khac: "",
                loai_chung_chi: "ngoai_ngu",
                ngay_cap: "",
                ngay_het_han: "",
                diem_so: "",
                xep_loai: "",
                file_dinh_kem: null,
                trang_thai: 0,
                is_locked: false
            },
            isDonViKhac: false,
            fileChon: null,
            itemDuocChon: null,
            itemXoa: null,
            instanceModal: null,
            instanceModalChiTiet: null,
            instanceModalXoa: null,
            trangHienTai: 1,
            soBanGhiTrenTrang: 10
        };
    },
    watch: {
        kieuSapXep() {
            if(this.danhSach) this.danhSach = [...this.danhSach].sort((a, b) => this.kieuSapXep === 'newest' ? b.id - a.id : a.id - b.id);
        },
        
        tuKhoaTimKiem() {
            this.trangHienTai = 1;
        }
    },
    mounted() {
        this.layDuLieu();
        this.laySinhVien();
        this.layDonViCap();
        if (window.bootstrap) {
            this.instanceModal = new window.bootstrap.Modal(this.$refs.refsModal);
            this.instanceModalChiTiet = new window.bootstrap.Modal(this.$refs.refsModalChiTiet);
            this.instanceModalXoa = new window.bootstrap.Modal(this.$refs.modalElementXoa);

        }
    },
    computed: {
        danhSachSinhVienLoc() {
            if (!this.tuKhoaTimKiemSinhVien) return this.danhSachSinhVien;
            const kw = this.tuKhoaTimKiemSinhVien.toLowerCase();
            return this.danhSachSinhVien.filter(sv =>
                sv.ho_ten?.toLowerCase().includes(kw) ||
                sv.ma_sinh_vien?.toLowerCase().includes(kw)
            );
        },
        danhSachLoc() {
            if (!this.tuKhoaTimKiem) return this.danhSach;
            const kw = this.tuKhoaTimKiem.toLowerCase();
            return this.danhSach.filter(item =>
                item.ten_chung_chi.toLowerCase().includes(kw) ||
                item.ma_chung_chi.toLowerCase().includes(kw) ||
                item.sinh_vien?.ho_ten?.toLowerCase().includes(kw) ||
                item.sinh_vien?.ma_sinh_vien?.toLowerCase().includes(kw)
            );
        },
        danhSachPhanTrang() {
            const start = (this.trangHienTai - 1) * this.soBanGhiTrenTrang;
            return this.danhSachLoc.slice(start, start + this.soBanGhiTrenTrang);
        },
        tongSoTrang() {
            return Math.ceil(this.danhSachLoc.length / this.soBanGhiTrenTrang);
        },
        cacTrangHienThi() {
            const current = this.trangHienTai;
            const total = this.tongSoTrang;
            if (total <= 3) return Array.from({ length: total }, (_, i) => i + 1);
            if (current === 1) return [1, 2, 3];
            if (current === total) return [total - 2, total - 1, total];
            return [current - 1, current, current + 1];
        }
    },
    methods: {
        layTenTrangThai(trangThai) {
            if (trangThai == 1) return 'Đã Đúc NFT';
            if (trangThai == 2) return 'Chờ Duyệt';
            if (trangThai == 3) return 'Đã Thu Hồi';
            return 'Chưa Đúc NFT';
        },
        layLopTrangThai(trangThai) {
            if (trangThai == 1) return 'bg-success-subtle text-success border border-success';
            if (trangThai == 2) return 'bg-warning-subtle text-warning border border-warning';
            if (trangThai == 3) return 'bg-danger-subtle text-danger border border-danger';
            return 'bg-secondary-subtle text-secondary border border-secondary';
        },
        layTenDonVi(item) {
            if (!item) return '---';
            if (item.don_vi_cap_id) {
                const unit = this.danhSachDonViCap.find(u => u.id == item.don_vi_cap_id);
                return unit ? unit.ten_don_vi : (item.don_vi_cap?.ten_don_vi || '---');
            }
            return item.ten_don_vi_cap_khac || '---';
        },
        getTenLoaiChungChi(loai) {
            const map = {
                'ngoai_ngu': 'Ngoại ngữ',
                'tin_hoc': 'Tin học',
                'ky_nang': 'Kỹ năng',
                'bang_cap': 'Bằng cấp',
                'khac': 'Khác'
            };
            return map[loai] || loai;
        },
        layFullUrl(path) {
            if (!path) return '#';
            if (path.startsWith('http')) return path;
            return 'http://127.0.0.1:8000/' + path;
        },
        layDuLieu() {
            this.dangTai = true;
            baseRequestAdmin.get("chung-chis/get-data")
                .then(res => {
                    this.danhSach = res.data.list || res.data.data || res.data;
                    if(this.danhSach) this.danhSach = [...this.danhSach].sort((a, b) => this.kieuSapXep === 'newest' ? b.id - a.id : a.id - b.id);
                })
                .catch(err => {
                    console.error("Lỗi lấy data chứng chỉ:", err);
                    this.$toast.error("Không thể tải danh sách chứng chỉ!");
                })
                .finally(() => {
                    this.dangTai = false;
                });
        },

        laySinhVien() {
            baseRequestAdmin.get("sinh-viens/get-data")
                .then(res => {
                    this.danhSachSinhVien = res.data.data || [];
                })
                .catch(err => console.error(err));
        },
        layDonViCap() {
            baseRequestAdmin.get("don-vi-caps/get-data")
                .then(res => {
                    this.danhSachDonViCap = res.data.data || [];
                })
                .catch(err => console.error(err));
        },

        moModalThem() {
            this.laCapNhat = false;
            this.tuKhoaTimKiemSinhVien = "";
            this.fileChon = null;
            this.isDonViKhac = false;
            this.duLieuForm = {
                id: null, ma_chung_chi: "", ten_chung_chi: "", sinh_vien_id: "",
                don_vi_cap_id: "", ten_don_vi_cap_khac: "", loai_chung_chi: "ngoai_ngu", 
                ngay_cap: "", ngay_het_han: "",
                diem_so: "", xep_loai: "", file_dinh_kem: null, trang_thai: 0, is_locked: false
            };
            this.instanceModal.show();
        },
        moModalSua(item) {
            this.laCapNhat = true;
            this.tuKhoaTimKiemSinhVien = "";
            this.fileChon = null;
            this.duLieuForm = { ...item };
            this.isDonViKhac = !!item.ten_don_vi_cap_khac;
            this.instanceModal.show();
        },
        moModalChiTiet(item) {
            this.itemDuocChon = item;
            this.instanceModalChiTiet.show();
        },
        chonFile(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    this.$toast.error("File không được vượt quá 2MB");
                    return;
                }
                this.fileChon = file;
            }
        },
        xuLyLuu() {
            this.dangLuu = true;
            
            // Xử lý loại bỏ dữ liệu thừa trước khi tạo FormData
            const payload = { ...this.duLieuForm };
            if (this.isDonViKhac) {
                payload.don_vi_cap_id = null;
            } else {
                payload.ten_don_vi_cap_khac = null;
            }

            const formData = new FormData();
            Object.keys(payload).forEach(key => {
                if (key !== 'file_dinh_kem' && payload[key] !== null) {
                    formData.append(key, payload[key]);
                }
            });

            if (this.fileChon) {
                formData.append('file_dinh_kem', this.fileChon);
            }

            let request;
            if (this.laCapNhat) {
                formData.append('_method', 'PUT');
                request = baseRequestAdmin.post(`chung-chis/update/${this.duLieuForm.id}`, formData);
            } else {
                request = baseRequestAdmin.post("chung-chis/create", formData);
            }

            request
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
                    console.error(err);
                    const listErr = err.response?.data?.errors;
                    if (listErr) {
                        Object.values(listErr).forEach((error) => {
                            this.$toast.error(error[0]);
                        });
                    } else {
                        this.$toast.error("Lỗi hệ thống khi lưu chứng chỉ!");
                    }
                })
                .finally(() => {
                    this.dangLuu = false;
                });
        },
        xuLyXoa(item) {
            this.itemXoa = item;
            this.instanceModalXoa.show();
        },
        xacNhanXoa() {
            if (!this.itemXoa) return;
            this.dangLuu = true;
            baseRequestAdmin.delete(`chung-chis/delete/${this.itemXoa.id}`)
                .then(res => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.instanceModalXoa.hide();
                        this.layDuLieu();
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.$toast.error("Lỗi khi xóa chứng chỉ!");
                })
                .finally(() => {
                    this.dangLuu = false;
                });
        }
    }
};
</script>

<style scoped>
.btn-rose-v2 {
    background: var(--primary-darker);
    color: #fff;
    border: none;
}

.bg-rose-v2 {
    background: linear-gradient(135deg, var(--primary-darker), #BE123C);
}

.btn-rose-v2:hover {
    background: #E11D48;
    color: #fff;
}

.btn-action {
    width: 34px;
    height: 34px;
    padding: 0;
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-action:hover {
    transform: translateY(-2px);
    background: var(--primary);
}

.flux-input {
    border-radius: 10px;
    border: 1px solid var(--border-color);
    padding: 10px 14px;
    font-size: 13.5px;
}

.flux-input:focus {
    border-color: var(--primary-darker);
    box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.1);
}

.text-rose-dark {
    color: var(--primary-darker);
}

.text-rose {
    color: var(--primary);
}

.fw-800 {
    font-weight: 800;
}

.fw-600 {
    font-weight: 600;
}

.fw-700 {
    font-weight: 700;
}

.bg-rose-subtle {
    background-color: rgba(244, 63, 94, 0.1);
}

.detail-group {
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    height: 100%;
}
</style>
