<template>
    <div class="lop-hoc-view px-2">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title text-dark fw-800">Đăng ký lớp học</h3>
                <p class="page-subtitle text-muted">Tìm kiếm và đăng ký tham gia các lớp học mới theo môn học và năm học.</p>
            </div>
            <button class="btn btn-light-pink shadow-sm" @click="layDuLieu">
                <i class="bi bi-arrow-clockwise me-1"></i> Làm mới
            </button>
        </div>

        <!-- Student Status Warning -->
        <div v-if="trangThaiSV !== null && trangThaiSV !== 1" class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4" style="border-radius: 16px;">
            <div class="alert-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-800">Hạn chế đăng ký học phần</h6>
                <p class="mb-0 small fw-600 opacity-75">Hiện tại bạn đang ở trạng thái <b>{{ tenTrangThaiSV }}</b> nên không thể thực hiện đăng ký các lớp học mới.</p>
            </div>
        </div>

        <!-- Filter Area -->
        <div class="filter-card p-4 bg-white shadow-sm mb-4" style="border-radius: 20px;">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-700 text-muted small text-uppercase">Chọn môn học</label>
                    <select class="form-select flux-input" v-model="boLoc.mon_hoc_id">
                        <option value="">Tất cả môn học</option>
                        <option v-for="mon in danhSachMon" :key="mon.id" :value="mon.id">
                            {{ mon.ten_mon_hoc }} ({{ mon.ma_mon_hoc }})
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-700 text-muted small text-uppercase">Năm học</label>
                    <input type="text" class="form-control flux-input" v-model="boLoc.nam_hoc" placeholder="VD: 2023-2024">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-700 text-muted small text-uppercase">Trạng thái lớp</label>
                    <select class="form-select flux-input" v-model="boLoc.trang_thai">
                        <option value="">Tất cả trạng thái</option>
                        <option value="sap_bat_dau">Sắp bắt đầu (Mở đăng ký)</option>
                        <option value="dang_mo">Đang học</option>
                        <option value="da_ket_thuc">Đã kết thúc</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-accent w-100 fw-700 border-0 shadow-sm py-2" style="border-radius: 12px;" @click="layDuLieu">
                        <i class="bi bi-filter me-1"></i> Lọc dữ liệu
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="data-card shadow-sm border-0 bg-white" style="border-radius: 20px; overflow: hidden;">
            <div class="table-responsive">
                <table class="flux-table text-nowrap">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">STT</th>
                            <th>Thông tin Lớp học</th>
                            <th>Môn học</th>
                            <th>Giảng viên</th>
                            <th class="text-center">Sỉ số</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center px-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="dangTai">
                            <td colspan="7" class="text-center py-5">
                                <div class="spinner-border text-accent" role="status"></div>
                            </td>
                        </tr>
                        <tr v-else-if="danhSach.length === 0">
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state py-4">
                                    <i class="bi bi-inbox display-4 opacity-25 text-muted"></i>
                                    <p class="mt-3 text-muted fw-600">Vui lòng chọn bộ lọc để tìm kiếm lớp học phù hợp.</p>
                                </div>
                            </td>
                        </tr>
                        <tr v-else v-for="(item, index) in danhSach" :key="item.id">
                            <td class="text-center text-muted fw-600">#{{ index + 1 }}</td>
                            <td>
                                <div class="fw-800 text-dark">{{ item.ten_lop_hoc }}</div>
                                <div class="small text-muted">{{ item.ma_lop_hoc }} | HK{{ item.hoc_ky }} - {{ item.nam_hoc }}</div>
                            </td>
                            <td>
                                <div class="fw-700 text-accent">{{ item.mon_hoc?.ten_mon_hoc }}</div>
                                <div class="small text-muted">{{ item.mon_hoc?.so_tin_chi }} Tín chỉ</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm bg-pink-subtle text-accent fw-800">{{ item.giang_vien?.ho_ten?.charAt(0) }}</div>
                                    <div>
                                        <div class="fw-700 text-dark text-truncate" style="max-width: 150px;">{{ item.giang_vien?.ho_ten || 'Đang cập nhật' }}</div>
                                        <div class="small text-muted">{{ item.giang_vien?.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="fw-800 text-dark">{{ item.si_so || 0 }}</span>
                                <span class="text-muted small"> / 40</span>
                            </td>
                            <td class="text-center">
                                <span v-if="item.trang_thai === 'sap_bat_dau'" class="badge badge-warning-subtle text-warning border border-warning-subtle fw-700">Sắp bắt đầu</span>
                                <span v-else-if="item.trang_thai === 'dang_mo'" class="badge badge-info-subtle text-info border border-info-subtle fw-700">Đang học</span>
                                <span v-else class="badge badge-success-subtle text-success border border-success-subtle fw-700">Đã kết thúc</span>
                            </td>
                            <td class="text-center">
                                <button v-if="item.trang_thai === 'sap_bat_dau' && !item.da_dang_ky" 
                                    class="btn btn-sm btn-accent-outline fw-700 px-3 py-1" 
                                    :class="{ 'opacity-50 cursor-not-allowed': trangThaiSV !== 1 }"
                                    :disabled="trangThaiSV !== 1"
                                    style="border-radius: 8px;"
                                    @click="moModalDangKy(item)">
                                    Đăng ký học
                                </button>
                                <div v-else-if="item.da_dang_ky" class="d-flex flex-column align-items-center gap-1">
                                    <span class="text-success fw-700 small">
                                        <i class="bi bi-check-circle-fill me-1"></i> Đã đăng ký
                                    </span>
                                    <button v-if="item.trang_thai === 'sap_bat_dau'" 
                                        class="btn btn-sm btn-outline-danger fw-700 px-3 py-0 mt-1" 
                                        style="font-size: 11px; border-radius: 6px;"
                                        @click="moModalHuyDangKy(item)">
                                        Hủy đăng ký
                                    </button>
                                </div>
                                <span v-else class="text-muted small fw-600">Không được phép đăng ký</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modals -->
        <div class="modal fade" id="dangKyModal" tabindex="-1" ref="modalDangKy">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                    <div class="modal-header border-0 bg-pink-gradient text-white p-4" style="border-radius: 24px 24px 0 0">
                        <h5 class="modal-title fw-800">Xác nhận Đăng ký</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <i class="bi bi-journal-plus text-accent display-1 opacity-25 mb-3 d-block"></i>
                        <h5 class="fw-800 text-dark mb-3">Bạn chắc chắn muốn đăng ký lớp học này?</h5>
                        <p class="text-muted mb-0">Lớp: <span class="text-accent fw-700">{{ lopDangChon?.ten_lop_hoc }}</span></p>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light-pink flex-fill py-2 fw-600" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn btn-pink flex-fill py-2 fw-800 shadow-pink" @click="xacNhanDangKy" :disabled="dangXuLy">
                            <span v-if="dangXuLy" class="spinner-border spinner-border-sm me-2"></span>
                            Đồng ý Đăng ký
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="huyDangKyModal" tabindex="-1" ref="modalHuyDangKy">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                    <div class="modal-header border-0 bg-danger text-white p-4" style="border-radius: 24px 24px 0 0">
                        <h5 class="modal-title fw-800">Xác nhận Hủy Đăng ký</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger display-1 opacity-25 mb-3 d-block"></i>
                        <h5 class="fw-800 text-dark mb-3">Bạn chắc chắn muốn hủy đăng ký?</h5>
                        <p class="text-muted mb-0">Lớp: <span class="text-danger fw-700">{{ lopDangChon?.ten_lop_hoc }}</span></p>
                        <p class="text-muted small mt-2">Hành động này không thể hoàn tác.</p>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light flex-fill py-2 fw-600" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-danger flex-fill py-2 fw-800" @click="xacNhanHuyDangKy" :disabled="dangXuLy">
                            <span v-if="dangXuLy" class="spinner-border spinner-border-sm me-2"></span>
                            Xác nhận Hủy
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
    name: "SinhVienLopHoc",
    data() {
        return {
            danhSach: [],
            danhSachMon: [],
            trangThaiSV: null,
            dangTai: false,
            dangXuLy: false,
            lopDangChon: null,
            instDangKy: null,
            instHuyDangKy: null,
            boLoc: {
                mon_hoc_id: "",
                nam_hoc: "",
                trang_thai: ""
            }
        };
    },
    computed: {
        tenTrangThaiSV() {
            switch(this.trangThaiSV) {
                case 0: return "Nghỉ Học";
                case 2: return "Bảo Lưu";
                case 3: return "Tốt Nghiệp";
                default: return "Không xác định";
            }
        }
    },
    mounted() {
        this.layDanhSachMon();
        this.layDuLieu();
        if (window.bootstrap) {
            this.instDangKy = new window.bootstrap.Modal(this.$refs.modalDangKy);
            this.instHuyDangKy = new window.bootstrap.Modal(this.$refs.modalHuyDangKy);
        }
    },
    methods: {
        layDanhSachMon() {
            baseRequestSinhVien.get("mon-hocs")
                .then(res => {
                    this.danhSachMon = res.data.data || [];
                });
        },
        layDuLieu() {
            this.dangTai = true;
            baseRequestSinhVien.get("lop-hocs/all", { params: this.boLoc })
                .then(res => {
                    this.danhSach = res.data.data || [];
                    this.trangThaiSV = res.data.trang_thai_sv;
                })
                .catch(err => {
                    this.$toast.error("Lỗi tải danh sách lớp học!");
                })
                .finally(() => {
                    this.dangTai = false;
                });
        },
        moModalDangKy(item) {
            if (this.trangThaiSV !== 1) {
                this.$toast.warning(`Bạn đang ở trạng thái ${this.tenTrangThaiSV}, không thể đăng ký!`);
                return;
            }
            this.lopDangChon = item;
            this.instDangKy?.show();
        },
        moModalHuyDangKy(item) {
            this.lopDangChon = item;
            this.instHuyDangKy?.show();
        },
        xacNhanDangKy() {
            if (!this.lopDangChon) return;
            this.dangXuLy = true;
            baseRequestSinhVien.post("lop-hocs/dang-ky", { lop_hoc_id: this.lopDangChon.id })
                .then(res => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.instDangKy?.hide();
                        this.layDuLieu(); // Làm mới danh sách
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch(err => {
                    this.$toast.error(err.response?.data?.message || "Lỗi đăng ký lớp học!");
                })
                .finally(() => {
                    this.dangXuLy = false;
                });
        },
        xacNhanHuyDangKy() {
            if (!this.lopDangChon) return;
            this.dangXuLy = true;
            baseRequestSinhVien.post("lop-hocs/huy-dang-ky", { lop_hoc_id: this.lopDangChon.id })
                .then(res => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.instHuyDangKy?.hide();
                        this.layDuLieu(); // Làm mới danh sách
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch(err => {
                    this.$toast.error(err.response?.data?.message || "Lỗi hủy đăng ký lớp học!");
                })
                .finally(() => {
                    this.dangXuLy = false;
                });
        }
    }
};
</script>

<style scoped>
.text-accent { color: #db2777; }
.btn-accent { background: #db2777; color: white; }
.btn-accent-outline { border: 1.5px solid #db2777; color: #db2777; background: transparent; }
.btn-accent-outline:hover:not(:disabled) { background: #db2777; color: white; }
.bg-pink-subtle { background: #fdf2f8; }
.flux-input {
    border: 1px solid #fce7f3;
    border-radius: 12px;
    padding: 10px 15px;
    font-size: 14px;
    transition: all 0.2s;
    background: #f8fafc;
}
.btn-light-pink {
    background: #fdf2f8; color: #db2777; border-radius: 12px;
    padding: 8px 16px; border: 1px solid #fce7f3; font-weight: 600;
}
.btn-pink {
    background: #db2777; color: white; border-radius: 14px;
    padding: 10px 20px; border: none; font-weight: 700; transition: all 0.3s;
}
.btn-pink:hover { background: #be185d; transform: translateY(-2px); }
.bg-pink-gradient { background: linear-gradient(135deg, #db2777 0%, #be185d 100%); }
.shadow-pink { box-shadow: 0 10px 15px -3px rgba(219,39,119,0.25); }
.flux-input:focus { border-color: #db2777; box-shadow: 0 0 0 3px rgba(219, 39, 119, 0.1); outline: none; background: white; }
.badge { padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; text-transform: uppercase; }
.badge-warning-subtle { background: #fffbeb; color: #d97706; }
.badge-info-subtle { background: #f0f9ff; color: #0284c7; }
.badge-success-subtle { background: #f0fdf4; color: #16a34a; }
.avatar-sm { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.btn-light-pink {
    background: #fdf2f8; color: #db2777; border-radius: 12px;
    padding: 8px 16px; border: 1px solid #fce7f3; font-weight: 700;
}
.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }
.fw-600 { font-weight: 600; }
.cursor-not-allowed { cursor: not-allowed; }
</style>
