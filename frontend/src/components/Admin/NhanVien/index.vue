<template>
    <div class="nhan-vien-management">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title">Quản lý Nhân Viên</h3>
                <p class="page-subtitle">Hệ thống quản trị tài khoản, chức vụ và phòng ban nội bộ.</p>
            </div>
            <button v-if="$hasPermission(24)" class="btn-new" @click="moModalThemMoi">
                <i class="bi bi-person-plus-fill"></i> Thêm nhân viên
            </button>
        </div>

        <!-- Main Content Card -->
        <div class="data-card shadow-sm border-0">
            <!-- Table Controls -->
            <div class="table-controls d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="navbar-search m-0" style="max-width: 400px; width: 100%;">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" v-model="tuKhoaTimKiem" placeholder="Tìm theo tên, email hoặc mã NV..." />
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
                            <th width="120">Mã NV</th>
                            <th>Họ và Tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Địa chỉ</th>
                            <th>Chức vụ</th>
                            <th>Phòng ban</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end pe-3" width="160">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="dangTai">
                            <td colspan="10" class="text-center py-5">
                                <div class="spinner-border text-rose" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="danhSachPhanTrang.length === 0">
                            <td colspan="10" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-people d-block mb-2 opacity-25" style="font-size: 3rem"></i>
                                    <span class="text-muted">Không tìm thấy nhân viên nào.</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else v-for="(item, index) in danhSachPhanTrang" :key="item.id">
                            <td class="fw-700 text-muted text-center">#{{ (trangHienTai - 1) *
                                soBanGhiTrenTrang + index + 1 }}</td>
                            <td class="fw-700 text-rose-dark">{{ item.ma_nhan_vien || '---' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm flex-shrink-0 me-3"
                                        style="background: rgba(190, 18, 60, 0.08); color: var(--primary-darker);">
                                        {{ layKyTuDau(item.ho_ten) }}
                                    </div>
                                    <div class="fw-700 text-main">{{ item.ho_ten }}</div>
                                </div>
                            </td>
                            <td class="small text-muted">{{ item.email }}</td>
                            <td class="small fw-600 text-rose-dark">{{ item.so_dien_thoai || '---' }}</td>
                            <td class="small text-muted">{{ item.dia_chi || '---' }}</td>
                            <td>
                                <span class="badge-role">{{ layTenChucVu(item.chuc_vu_id) || item.chuc_vu || 'N/A'
                                    }}</span>
                            </td>
                            <td class="small fw-600">
                                {{ layTenPhongBan(item.phong_ban_id) || item.phong_ban || 'N/A' }}
                            </td>
                            <td class="text-center">
                                <button class="btn btn-status-toggle" :class="layLopTrangThai(item.trang_thai)"
                                    @click="xuLyDoiTrangThai(item)" :disabled="!$hasPermission(25)">
                                    {{ layNhanTrangThai(item.trang_thai) }}
                                </button>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-action shadow-sm" @click="moModalChiTiet(item.id)"
                                        title="Xem chi tiết">
                                        <i class="bi bi-eye-fill text-info"></i>
                                    </button>
                                    <button v-if="$hasPermission(25)" class="btn btn-action shadow-sm" @click="moModalCapNhat(item)"
                                        title="Cập nhật">
                                        <i class="bi bi-pencil-fill text-primary-darker"></i>
                                    </button>
                                    <button v-if="$hasPermission(25)" class="btn btn-action shadow-sm" @click="xuLyXoa(item)" title="Xóa">
                                        <i class="bi bi-trash-fill text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="table-footer d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top mt-3"
                v-if="tongSoTrang > 1">
                <span class="small text-muted mb-3 mb-md-0">
                    Hiển thị <b>{{ danhSachPhanTrang.length > 0 ? (trangHienTai - 1) * soBanGhiTrenTrang + 1 : 0 }}</b>
                    - <b>{{ Math.min(trangHienTai * soBanGhiTrenTrang, danhSachLoc.length) }}</b>
                    trong tổng số <b>{{ danhSachLoc.length }}</b> nhân viên
                </span>
                <nav>
                    <ul class="pagination pagination-sm m-0 gap-1">
                        <li class="page-item" :class="{ disabled: trangHienTai === 1 }">
                            <a class="page-link border-0 rounded-circle" href="#" @click.prevent="trangHienTai--">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item" v-for="p in cacTrangHienThi" :key="p"
                            :class="{ active: trangHienTai === p }">
                            <a class="page-link border-0 rounded-circle shadow-sm" href="#"
                                @click.prevent="trangHienTai = p">{{ p }}</a>
                        </li>
                        <li class="page-item" :class="{ disabled: trangHienTai === tongSoTrang }">
                            <a class="page-link border-0 rounded-circle" href="#" @click.prevent="trangHienTai++">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Edit/Create Modal -->
        <div class="modal fade" id="nhanVienModal" tabindex="-1" aria-hidden="true" ref="modalEle">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
                    <div class="modal-header border-0 bg-rose-v2 text-white p-4">
                        <h5 class="modal-title fw-800">{{ laCapNhat ? 'Cập nhật tài khoản' : 'Tạo tài khoản mới' }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form @submit.prevent="xuLyLuu">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Họ và Tên</label>
                                    <input type="text" class="form-control flux-input" v-model="duLieuBieuMau.ho_ten"
                                        placeholder="Ví dụ: Nguyễn Văn A">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Email Công
                                        Việc</label>
                                    <input type="email" class="form-control flux-input" v-model="duLieuBieuMau.email"
                                        placeholder="name@company.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Chức vụ</label>
                                    <select class="form-select flux-input" v-model="duLieuBieuMau.chuc_vu_id">
                                        <option value="">Chọn chức vụ</option>
                                        <option v-for="cv in danhSachChucVu" :key="cv.id" :value="cv.id">
                                            {{ cv.ten_chuc_vu }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Phòng ban</label>
                                    <select class="form-select flux-input" v-model="duLieuBieuMau.phong_ban_id">
                                        <option value="">Chọn phòng ban</option>
                                        <option v-for="pb in danhSachPhongBan" :key="pb.id" :value="pb.id">
                                            {{ pb.ten_phong_ban }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Số điện
                                        thoại</label>
                                    <input type="text" class="form-control flux-input"
                                        v-model="duLieuBieuMau.so_dien_thoai" placeholder="090...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Trạng thái</label>
                                    <select class="form-select flux-input" v-model="duLieuBieuMau.trang_thai">
                                        <option :value="1">Hoạt động</option>
                                        <option :value="0">Tạm nghỉ</option>
                                        <option :value="2">Nghỉ việc</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Địa chỉ</label>
                                    <textarea class="form-control flux-input" v-model="duLieuBieuMau.dia_chi"
                                        rows="2"></textarea>
                                </div>
                            </div>

                            <div class="mt-4 pt-2 d-flex gap-3">
                                <button type="button" class="btn btn-light border px-4 flex-fill fw-600"
                                    data-bs-dismiss="modal">Hủy bỏ</button>
                                <button type="submit" class="btn btn-rose-v2 px-4 flex-fill fw-700 shadow-sm"
                                    :disabled="dangLuu">
                                    <span v-if="dangLuu" class="spinner-border spinner-border-sm me-1"></span>
                                    {{ laCapNhat ? 'Cập nhật ngay' : 'Xác nhận tạo' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="chiTietNhanVienModal" tabindex="-1" aria-hidden="true" ref="modalChiTietEle">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
                    <div class="modal-header border-0 bg-rose-v2 text-white p-4">
                        <h5 class="modal-title fw-800">Chi tiết nhân viên</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div v-if="duLieuChiTiet">
                            <div class="bg-rose-v2 text-white text-center py-5"
                                style="border-radius: 0 0 20px 20px; margin-top: -1px;">
                                <div class="avatar-xl mx-auto mb-3 shadow-lg border border-3 border-white rounded-circle overflow-hidden d-flex align-items-center justify-content-center"
                                    style="width: 100px; height: 100px; background: white;">
                                    <img v-if="duLieuChiTiet.hinh_anh" :src="duLieuChiTiet.hinh_anh" class="w-100 h-100 object-fit-cover" />
                                    <div v-else class="fw-bold text-rose-v2 fs-1">{{ layKyTuDau(duLieuChiTiet.ho_ten) }}</div>
                                </div>
                                <h4 class="fw-800 mb-1">{{ duLieuChiTiet.ho_ten }}</h4>
                                <div class="opacity-75 fs-6">{{ duLieuChiTiet.email }}</div>
                            </div>
                            <div class="p-4">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div
                                            class="d-flex align-items-center p-3 border rounded-3 bg-light-subtle h-100 shadow-sm">
                                            <div class="flex-shrink-0 me-3 bg-rose-subtle text-rose rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-person-badge fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted text-uppercase fw-bold mb-1"
                                                    style="font-size: 0.75rem;">Mã nhân viên</div>
                                                <div class="fw-bold text-dark mb-0 fs-6">{{ duLieuChiTiet.ma_nhan_vien
                                                    || '---' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div
                                            class="d-flex align-items-center p-3 border rounded-3 bg-light-subtle h-100 shadow-sm">
                                            <div class="flex-shrink-0 me-3 bg-rose-subtle text-rose rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-telephone fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted text-uppercase fw-bold mb-1"
                                                    style="font-size: 0.75rem;">Số điện thoại</div>
                                                <div class="fw-bold text-dark mb-0 fs-6">{{ duLieuChiTiet.so_dien_thoai
                                                    || '---' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div
                                            class="d-flex align-items-center p-3 border rounded-3 bg-light-subtle h-100 shadow-sm">
                                            <div class="flex-shrink-0 me-3 bg-rose-subtle text-rose rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-briefcase fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted text-uppercase fw-bold mb-1"
                                                    style="font-size: 0.75rem;">Chức vụ</div>
                                                <div class="fw-bold text-dark mb-0 fs-6">{{
                                                    layTenChucVu(duLieuChiTiet.chuc_vu_id) || duLieuChiTiet.chuc_vu ||
                                                    '---' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div
                                            class="d-flex align-items-center p-3 border rounded-3 bg-light-subtle h-100 shadow-sm">
                                            <div class="flex-shrink-0 me-3 bg-rose-subtle text-rose rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-building fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted text-uppercase fw-bold mb-1"
                                                    style="font-size: 0.75rem;">Phòng ban</div>
                                                <div class="fw-bold text-dark mb-0 fs-6">{{
                                                    layTenPhongBan(duLieuChiTiet.phong_ban_id) ||
                                                    duLieuChiTiet.phong_ban || '---' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div
                                            class="d-flex align-items-center p-3 border rounded-3 bg-light-subtle h-100 shadow-sm">
                                            <div class="flex-shrink-0 me-3 bg-rose-subtle text-rose rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-geo-alt fs-5"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="small text-muted text-uppercase fw-bold mb-1"
                                                    style="font-size: 0.75rem;">Địa chỉ</div>
                                                <div class="fw-bold text-dark mb-0 fs-6 text-truncate">{{
                                                    duLieuChiTiet.dia_chi || '---' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-5">
                            <div class="spinner-border text-info" role="status"></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light border px-4 pb-2 pt-2 fw-600 rounded-3"
                            data-bs-dismiss="modal">Đóng cửa sổ</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteNhanVienModal" tabindex="-1" aria-hidden="true" ref="modalElementXoa">
            <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 400px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3">
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="fw-800 text-dark mb-2">Xác nhận xóa?</h4>
                        <p class="text-muted mb-4">Bạn có chắc chắn muốn xóa nhân viên <b>{{ itemXoa?.ho_ten }}</b>? Hành động này không thể hoàn tác.</p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light border flex-fill fw-600" data-bs-dismiss="modal">Hủy bỏ</button>
                            <button type="button" class="btn btn-danger flex-fill fw-700" @click="xacNhanXoa" :disabled="dangTai">
                                <span v-if="dangTai" class="spinner-border spinner-border-sm me-1"></span>
                                Xác nhận xóa
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
    name: "AdminNhanVien",
    data() {
        return {
            danhSach: [],
            danhSachChucVu: [],
            danhSachPhongBan: [],
            dangTai: false,
            dangLuu: false,
            tuKhoaTimKiem: "",
            kieuSapXep: 'newest',
            laCapNhat: false,
            trangHienTai: 1,
            soBanGhiTrenTrang: 10,
            duLieuBieuMau: {
                id: null,
                ho_ten: "",
                email: "",
                so_dien_thoai: "",
                dia_chi: "",
                chuc_vu_id: "",
                phong_ban_id: "",
                trang_thai: 1
            },
            duLieuChiTiet: null,
            itemXoa: null,
            modal: null,
            modalChiTiet: null,
            instanceModalXoa: null
        };
    },
    computed: {
        danhSachLoc() {
            let ketQua = this.danhSach;
            if (this.tuKhoaTimKiem.trim()) {
                const kw = this.tuKhoaTimKiem.toLowerCase();
                ketQua = ketQua.filter(item => {
                    const ten = (item.ho_ten || '').toLowerCase();
                    const email = (item.email || '').toLowerCase();
                    const ma = (item.ma_nhan_vien || item.id_nhan_vien || item.id || '').toString().toLowerCase();
                    return ten.includes(kw) || email.includes(kw) || ma.includes(kw);
                });
            }
            
            return ketQua;
        },
        danhSachPhanTrang() {
            const batDau = (this.trangHienTai - 1) * this.soBanGhiTrenTrang;
            return this.danhSachLoc.slice(batDau, batDau + this.soBanGhiTrenTrang);
        },
        tongSoTrang() {
            return Math.ceil(this.danhSachLoc.length / this.soBanGhiTrenTrang);
        },
        cacTrangHienThi() {
            const hienTai = this.trangHienTai;
            const tong = this.tongSoTrang;
            if (tong <= 3) return Array.from({ length: tong }, (_, i) => i + 1);
            if (hienTai === 1) return [1, 2, 3];
            if (hienTai === tong) return [tong - 2, tong - 1, tong];
            return [hienTai - 1, hienTai, hienTai + 1];
        }
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
        this.layChucVu();
        this.layPhongBan();
        if (window.bootstrap) {
            this.modal = new window.bootstrap.Modal(this.$refs.modalEle);
            this.modalChiTiet = new window.bootstrap.Modal(this.$refs.modalChiTietEle);
            this.instanceModalXoa = new window.bootstrap.Modal(this.$refs.modalElementXoa);
        }
    },
    methods: {
        layKyTuDau(name) {
            if (!name) return '??';
            const parts = name.split(' ');
            if (parts.length === 1) return parts[0][0].toUpperCase();
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        },
        layNhanTrangThai(status) {
            const labels = {
                1: 'Hoạt động',
                0: 'Tạm nghỉ',
                2: 'Nghỉ việc'
            };
            return labels[status] || 'Không xác định';
        },
        layLopTrangThai(status) {
            const classes = {
                1: 'status-active',
                0: 'status-warning',
                2: 'status-danger'
            };
            return classes[status] || '';
        },
        layDuLieu() {
            this.dangTai = true;
            baseRequestAdmin.get("nhan-viens/get-data")
                .then(res => {
                    this.danhSach = res.data.list || res.data.data || res.data;
                    if(this.danhSach) this.danhSach = [...this.danhSach].sort((a, b) => this.kieuSapXep === 'newest' ? b.id - a.id : a.id - b.id);
                })
                .catch(err => {
                    console.error("Lỗi lấy danh sách nhân viên:", err);
                })
                .finally(() => {
                    this.dangTai = false;
                });
        },
        layChucVu() {
            baseRequestAdmin.get("chuc-vus/get-data")
                .then(res => {
                    this.danhSachChucVu = res.data.list || res.data.data || res.data;
                })
                .catch(err => {
                    console.error("Lỗi lấy danh sách chức vụ:", err);
                });
        },
        layPhongBan() {
            baseRequestAdmin.get("phong-bans/get-data")
                .then(res => {
                    this.danhSachPhongBan = res.data.list || res.data.data || res.data;
                })
                .catch(err => {
                    console.error("Lỗi lấy danh sách phòng ban:", err);
                });
        },
        layTenChucVu(id) {
            const cv = this.danhSachChucVu.find(i => i.id === id);
            return cv ? cv.ten_chuc_vu : null;
        },
        layTenPhongBan(id) {
            const pb = this.danhSachPhongBan.find(i => i.id === id);
            return pb ? pb.ten_phong_ban : null;
        },

        moModalThemMoi() {
            this.laCapNhat = false;
            this.duLieuBieuMau = { id: null, ho_ten: "", email: "", so_dien_thoai: "", dia_chi: "", chuc_vu_id: "", phong_ban_id: "", trang_thai: 1 };
            this.modal.show();
        },
        moModalCapNhat(item) {
            this.laCapNhat = true;
            this.duLieuBieuMau = { ...item };
            this.modal.show();
            baseRequestAdmin.get(`nhan-viens/detail/${item.id}`)
                .then(res => {
                    if (res.data.data) {
                        this.duLieuBieuMau = { ...res.data.data };
                    }
                })
                .catch(err => {
                    console.error(err);
                });
        },
        xuLyDoiTrangThai(item) {
            const nextStatus = (item.trang_thai + 1) % 3;
            baseRequestAdmin.post("nhan-viens/change-status", {
                id: item.id,
                trang_thai: nextStatus
            })
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        item.trang_thai = nextStatus;
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch((err) => {
                    const listErr = err.response.data.errors;
                    Object.values(listErr).forEach((error) => {
                        this.$toast.error(error[0]);
                    });
                });
        },
        moModalChiTiet(id) {
            this.duLieuChiTiet = null;
            this.modalChiTiet.show();
            baseRequestAdmin.get(`nhan-viens/detail/${id}`)
                .then(res => {
                    if (res.data.status) {
                        this.duLieuChiTiet = res.data.data;
                    } else {
                        this.$toast.error(res.data.message);
                        this.modalChiTiet.hide();
                    }
                })
                .catch((err) => {
                    this.modalChiTiet.hide();
                    const listErr = err.response.data.errors;
                    Object.values(listErr).forEach((error) => {
                        this.$toast.error(error[0]);
                    });
                });
        },
        xuLyLuu() {
            this.dangLuu = true;
            const processCode = this.laCapNhat
                ? baseRequestAdmin.put(`nhan-viens/update/${this.duLieuBieuMau.id}`, this.duLieuBieuMau)
                : baseRequestAdmin.post("nhan-viens/create", this.duLieuBieuMau);

            processCode
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.modal.hide();
                        this.layDuLieu();
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch((err) => {
                    const listErr = err.response.data.errors;
                    Object.values(listErr).forEach((error) => {
                        this.$toast.error(error[0]);
                    });
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
            this.dangTai = true;
            baseRequestAdmin.delete(`nhan-viens/delete/${this.itemXoa.id}`)
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.instanceModalXoa.hide();
                        this.layDuLieu();
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch((err) => {
                    console.error(err);
                    const listErr = err.response?.data?.errors;
                    if (listErr) {
                        Object.values(listErr).forEach((error) => {
                            this.$toast.error(error[0]);
                        });
                    } else {
                        this.$toast.error("Lỗi hệ thống khi xóa nhân viên!");
                    }
                })
                .finally(() => {
                    this.dangTai = false;
                });
        }
    }
};
</script>
