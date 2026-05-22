<template>
    <div class="sinh-vien-management">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title">Quản lý Sinh viên</h3>
                <p class="page-subtitle">Danh sách sinh viên, trạng thái và thông tin đào tạo.</p>
            </div>
            <button v-if="$hasPermission(27)" class="btn-new" @click="moModalThemMoi">
                <i class="bi bi-person-plus-fill"></i> Thêm sinh viên
            </button>
        </div>

        <!-- Main Content Card -->
        <div class="data-card shadow-sm border-0">
            <!-- Table Controls -->
            <div class="table-controls d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="navbar-search m-0" style="max-width: 400px; width: 100%;">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" v-model="tuKhoaTimKiem" placeholder="Tìm theo tên, email hoặc mã SV..." />
                </div>
                <div class="d-flex gap-2">
                    <div class="d-flex align-items-center gap-2">
                    <select class="form-select btn-light-pink border-pink fw-600" style="width: auto;" v-model="kieuSapXep">
                        <option value="newest">Mới nhất</option>
                        <option value="oldest">Cũ nhất</option>
                    </select>
                    <button class="btn btn-light border" @click="layDuLieu">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
                </div>
            </div>

            <!-- Table Content -->
            <div class="table-responsive">
                <table class="flux-table text-nowrap">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">STT</th>
                            <th width="120">Mã SV</th>
                            <th>Họ và tên</th>
                            <th>Ngành Học</th>
                            <th>Email</th>
                            <th>Năm Bắt Đầu</th>
                            <th>Số Năm Học</th>
                            <th class="text-center">SĐT</th>
                            <th>Địa Chỉ</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end pe-3" width="160">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="dangTai">
                            <td colspan="11" class="text-center py-5">
                                <div class="spinner-border text-rose" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="danhSachPhanTrang.length === 0">
                            <td colspan="11" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-people d-block mb-2 opacity-25" style="font-size: 3rem"></i>
                                    <span class="text-muted">Không tìm thấy sinh viên nào.</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else v-for="(item, index) in danhSachPhanTrang" :key="item.id">
                            <td class="fw-700 text-muted text-center">
                                #{{ (trangHienTai - 1) * soBanGhiTrenTrang + index + 1 }}
                            </td>
                            <td class="fw-700 text-rose-dark">{{ item.ma_sinh_vien || '---' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm flex-shrink-0 me-3"
                                        style="background: rgba(190, 18, 60, 0.08); color: var(--primary-darker);">
                                        {{ layKyTuDau(item.ho_ten) }}
                                    </div>
                                    <div class="fw-700 text-main">{{ item.ho_ten }}</div>
                                </div>
                            </td>
                            <td class="small fw-600">{{ item.nganh_hoc }}</td>
                            <td class="small">{{ item.email }}</td>
                            <td class="small text-muted text-center">{{ item.nam_bat_dau }}</td>
                            <td class="small fw-600 text-center">{{ item.so_nam_hoc }}</td>
                            <td class="small fw-600 text-center text-rose-dark">{{ item.so_dien_thoai }}</td>
                            <td class="small">{{ item.dia_chi }}</td>
                            <td class="text-center">
                                <span class="badge-status" :class="layLopTrangThai(item.trang_thai)">
                                    {{ layNhanTrangThai(item.trang_thai) }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-action shadow-sm" @click="moModalChiTiet(item.id)"
                                        title="Xem chi tiết">
                                        <i class="bi bi-eye-fill text-info"></i>
                                    </button>
                                    <button v-if="$hasPermission(27)" class="btn btn-action shadow-sm" @click="moModalCapNhat(item)"
                                        title="Cập nhật">
                                        <i class="bi bi-pencil-fill text-primary-darker"></i>
                                    </button>
                                    <button v-if="$hasPermission(27)" class="btn btn-action shadow-sm" @click="xuLyXoa(item)" title="Xóa"
                                        :disabled="[0, 1, 2, 3].includes(item.trang_thai)">
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
                    trong tổng số <b>{{ danhSachLoc.length }}</b> sinh viên
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

        <!-- Create/Edit Modal -->
        <div class="modal fade" id="sinhVienModal" tabindex="-1" aria-hidden="true" ref="modalEle">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
                    <div class="modal-header border-0 bg-rose-v2 text-white p-4">
                        <h5 class="modal-title fw-800">{{ laCapNhat ? 'Cập nhật sinh viên' : 'Tạo sinh viên mới' }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form @submit.prevent="xuLyLuu">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Họ và tên</label>
                                    <input type="text" class="form-control flux-input" v-model="duLieuBieuMau.ho_ten"
                                        placeholder="Nguyễn Văn A" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Email</label>
                                    <input type="email" class="form-control flux-input" v-model="duLieuBieuMau.email"
                                        placeholder="name@university.edu" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Số điện
                                        thoại</label>
                                    <input type="text" class="form-control flux-input"
                                        v-model="duLieuBieuMau.so_dien_thoai" placeholder="090..." />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Ngành học</label>
                                    <input type="text" class="form-control flux-input" v-model="duLieuBieuMau.nganh_hoc"
                                        placeholder="Ngành học..." />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Số năm học</label>
                                    <input type="number" class="form-control flux-input"
                                        v-model="duLieuBieuMau.so_nam_hoc" placeholder="Ví dụ: 4" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Năm bắt đầu</label>
                                    <input type="number" class="form-control flux-input"
                                        v-model="duLieuBieuMau.nam_bat_dau" placeholder="Ví dụ: 2024" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Trạng thái</label>
                                    <select class="form-select flux-input" v-model="duLieuBieuMau.trang_thai">
                                        <option :value="1">Đang học</option>
                                        <option :value="0">Nghỉ học</option>
                                        <option :value="2">Bảo lưu</option>
                                        <option :value="3">Tốt nghiệp</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Địa chỉ</label>
                                    <input type="text" class="form-control flux-input" v-model="duLieuBieuMau.dia_chi"
                                        placeholder="Địa chỉ..." />
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
        <div class="modal fade" id="chiTietSinhVienModal" tabindex="-1" aria-hidden="true" ref="modalChiTietEle">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
                    <div class="modal-header border-0 bg-rose-v2 text-white p-4">
                        <h5 class="modal-title fw-800">Chi tiết sinh viên</h5>
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
                                                    style="font-size: 0.75rem;">Mã sinh viên</div>
                                                <div class="fw-bold text-dark mb-0 fs-6">{{ duLieuChiTiet.ma_sinh_vien
                                                    ||
                                                    '---' }}</div>
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
                                                    ||
                                                    '---' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div
                                            class="d-flex align-items-center p-3 border rounded-3 bg-light-subtle h-100 shadow-sm">
                                            <div class="flex-shrink-0 me-3 bg-rose-subtle text-rose rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-book fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted text-uppercase fw-bold mb-1"
                                                    style="font-size: 0.75rem;">Ngành học</div>
                                                <div class="fw-bold text-dark mb-0 fs-6">{{ duLieuChiTiet.nganh_hoc ||
                                                    '---' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div
                                            class="d-flex align-items-center p-3 border rounded-3 bg-light-subtle h-100 shadow-sm">
                                            <div class="flex-shrink-0 me-3 bg-rose-subtle text-rose rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-toggle-on fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted text-uppercase fw-bold mb-1"
                                                    style="font-size: 0.75rem;">Trạng thái</div>
                                                <div class="fw-bold text-dark mb-0 fs-6">
                                                    <span class="badge-status"
                                                        :class="layLopTrangThai(duLieuChiTiet.trang_thai)">
                                                        {{ layNhanTrangThai(duLieuChiTiet.trang_thai) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div
                                            class="d-flex align-items-center p-3 border rounded-3 bg-light-subtle h-100 shadow-sm">
                                            <div class="flex-shrink-0 me-3 bg-rose-subtle text-rose rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-calendar-event fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted text-uppercase fw-bold mb-1"
                                                    style="font-size: 0.75rem;">Năm bắt đầu</div>
                                                <div class="fw-bold text-dark mb-0 fs-6">{{ duLieuChiTiet.nam_bat_dau ||
                                                    '---' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div
                                            class="d-flex align-items-center p-3 border rounded-3 bg-light-subtle h-100 shadow-sm">
                                            <div class="flex-shrink-0 me-3 bg-rose-subtle text-rose rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-clock-history fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted text-uppercase fw-bold mb-1"
                                                    style="font-size: 0.75rem;">Số năm học</div>
                                                <div class="fw-bold text-dark mb-0 fs-6">{{ duLieuChiTiet.so_nam_hoc ||
                                                    '---' }}</div>
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
                                                <div class="fw-bold text-dark mb-0 fs-6 text-truncate"
                                                    :title="duLieuChiTiet.dia_chi">{{
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
                        <button type="button" class="btn btn-light border px-4 pb-2 pt-2 fw-600"
                            data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="xoaSinhVienModal" tabindex="-1" aria-hidden="true" ref="modalXoaEle">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3 text-danger">
                            <i class="bi bi-exclamation-octagon-fill" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="fw-800 mb-2">Xác nhận xóa?</h4>
                        <p class="text-muted mb-4">Bạn có chắc chắn muốn xóa sinh viên <br><b class="text-dark">{{
                            duLieuXoa.ho_ten }}</b>? <br>Hành động này không thể hoàn tác.</p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light border flex-fill fw-600 rounded-pill"
                                data-bs-dismiss="modal">Hủy bỏ</button>
                            <button type="button" class="btn btn-danger flex-fill fw-600 rounded-pill shadow-sm"
                                @click="xacNhanXoa" :disabled="dangLuu">
                                <span v-if="dangLuu" class="spinner-border spinner-border-sm me-1"></span>
                                Đồng ý xóa
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
    name: "AdminSinhVien",
    data() {
        return {
            danhSach: [],
            dangTai: false,
            dangLuu: false,
            tuKhoaTimKiem: "",
            kieuSapXep: 'newest',
            laCapNhat: false,
            trangHienTai: 1,
            soBanGhiTrenTrang: 10,
            duLieuBieuMau: {
                id: null,
                ma_sinh_vien: "",
                ho_ten: "",
                email: "",
                so_dien_thoai: "",
                nganh_hoc: "",
                so_nam_hoc: "",
                nam_bat_dau: "",
                dia_chi: "",
                trang_thai: 1
            },
            duLieuChiTiet: null,
            duLieuXoa: {},
            modal: null,
            modalChiTiet: null,
            modalXoa: null
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
                    const ma = (item.ma_sinh_vien || item.id || '').toString().toLowerCase();
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
        if (window.bootstrap) {
            this.modal = new window.bootstrap.Modal(this.$refs.modalEle);
            this.modalChiTiet = new window.bootstrap.Modal(this.$refs.modalChiTietEle);
            this.modalXoa = new window.bootstrap.Modal(this.$refs.modalXoaEle);
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
                1: 'Đang học',
                0: 'Nghỉ học',
                2: 'Bảo lưu',
                3: 'Tốt nghiệp'
            };
            return labels[status] || 'Không xác định';
        },
        layLopTrangThai(status) {
            const classes = {
                1: 'status-active',
                0: 'status-danger',
                2: 'status-warning',
                3: 'status-info'
    };
            return classes[status] || '';
        },
        layDuLieu() {
            this.dangTai = true;
            baseRequestAdmin.get("sinh-viens/get-data")
                .then(res => {
                    this.danhSach = res.data.list || res.data.data || res.data;
                    if(this.danhSach) this.danhSach = [...this.danhSach].sort((a, b) => this.kieuSapXep === 'newest' ? b.id - a.id : a.id - b.id);
                })
                .catch(err => {
                    console.error("Lỗi lấy danh sách sinh viên:", err);
                })
                .finally(() => {
                    this.dangTai = false;
                });
        },
        moModalThemMoi() {
            this.laCapNhat = false;
            this.duLieuBieuMau = { id: null, ma_sinh_vien: "", ho_ten: "", email: "", so_dien_thoai: "", nganh_hoc: "", so_nam_hoc: "", nam_bat_dau: "", dia_chi: "", trang_thai: 1 };
            this.modal.show();
        },
        moModalCapNhat(item) {
            this.laCapNhat = true;
            this.duLieuBieuMau = { ...item };
            this.modal.show();
            baseRequestAdmin.get(`sinh-viens/detail/${item.id}`)
                .then(res => {
                    if (res.data.data) {
                        this.duLieuBieuMau = { ...res.data.data };
                    }
                })
                .catch(err => {
                    console.error('Lỗi lấy chi tiết sinh viên', err);
                });
        },
        moModalChiTiet(id) {
            this.duLieuChiTiet = null;
            this.modalChiTiet.show();
            baseRequestAdmin.get(`sinh-viens/detail/${id}`)
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
                    const listErr = err.response?.data?.errors;
                    if (listErr) {
                        Object.values(listErr).forEach((error) => {
                            this.$toast.error(error[0]);
                        });
                    } else {
                        this.$toast.error("Có lỗi xảy ra khi lấy chi tiết!");
                    }
                });
        },
        // xuLyDoiTrangThai was removed as the API is not available
        xuLyLuu() {
            this.dangLuu = true;
            const processCode = this.laCapNhat
                ? baseRequestAdmin.put(`sinh-viens/update/${this.duLieuBieuMau.id}`, this.duLieuBieuMau)
                : baseRequestAdmin.post("sinh-viens/create", this.duLieuBieuMau);

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
                    const listErr = err.response?.data?.errors;
                    if (listErr) {
                        Object.values(listErr).forEach((error) => {
                            this.$toast.error(error[0]);
                        });
                    } else {
                        this.$toast.error("Lỗi hệ thống khi lưu sinh viên!");
                    }
                })
                .finally(() => {
                    this.dangLuu = false;
                });
        },
        xuLyXoa(item) {
            this.duLieuXoa = item;
            this.modalXoa.show();
        },
        xacNhanXoa() {
            this.dangLuu = true;
            baseRequestAdmin.delete(`sinh-viens/delete/${this.duLieuXoa.id}`)
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.modalXoa.hide();
                        this.layDuLieu();
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch((err) => {
                    const listErr = err.response?.data?.errors;
                    if (listErr) {
                        Object.values(listErr).forEach((error) => {
                            this.$toast.error(error[0]);
                        });
                    } else {
                        this.$toast.error("Lỗi khi xóa!");
                    }
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

.avatar-sm {
    width: 32px;
    height: 32px;
    background: var(--primary);
    color: var(--primary-text);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
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

.badge-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    min-width: 90px;
    display: inline-block;
}

.status-active {
    background: rgba(82, 183, 136, 0.15);
    color: #059669;
}

.status-active:hover {
    background: rgba(82, 183, 136, 0.25);
}

.status-warning {
    background: rgba(244, 162, 97, 0.15);
    color: #D97706;
}

.status-warning:hover {
    background: rgba(244, 162, 97, 0.25);
}

.status-danger {
    background: rgba(224, 82, 82, 0.15);
    color: #DC2626;
}

.status-danger:hover {
    background: rgba(224, 82, 82, 0.25);
}

.status-info {
    background: rgba(59, 130, 246, 0.15);
    color: #2563EB;
}

.text-rose-dark {
    color: var(--primary-darker);
}

.fw-800 {
    font-weight: 800;
}
</style>