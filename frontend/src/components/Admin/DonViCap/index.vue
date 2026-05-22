<template>
    <div class="don-vi-cap-management">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title">Quản lý Đơn Vị Cấp</h3>
                <p class="page-subtitle">Hệ thống quản lý các đơn vị cấp chứng chỉ và bằng cấp.</p>
            </div>
            <button v-if="$hasPermission(36)" class="btn-new" @click="moModalThem">
                <i class="bi bi-plus-circle-fill"></i> Thêm đơn vị cấp
            </button>
        </div>

        <!-- Main Content Card -->
        <div class="data-card shadow-sm border-0">
            <!-- Table Controls -->
            <div
                class="table-controls p-3 border-bottom bg-light-subtle d-flex justify-content-between align-items-center gap-3">
                <div class="navbar-search m-0" style="max-width: 400px; width: 100%;">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" v-model="tuKhoaTimKiem" placeholder="Tìm theo tên hoặc mã đơn vị..." />
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
                            <th>Mã đơn vị</th>
                            <th>Tên đơn vị</th>
                            <th>Loại đơn vị</th>
                            <th class="text-end" width="160">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="dangTai">
                            <td colspan="5" class="text-center py-5">
                                <div class="spinner-border text-rose" role="status"></div>
                            </td>
                        </tr>
                        <tr v-else-if="danhSachLoc.length === 0">
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-patch-check d-block mb-2 opacity-25" style="font-size: 3rem"></i>
                                    <span class="text-muted">Không tìm thấy đơn vị cấp nào.</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else v-for="(item, index) in danhSachPhanTrang" :key="item.id">
                            <td class="text-center text-muted fw-600">#{{ (trangHienTai - 1) * soBanGhiTrenTrang + index
                                + 1
                            }}</td>
                            <td class="fw-700 text-rose-dark">{{ item.ma_don_vi }}</td>
                            <td class="fw-700 text-main">{{ item.ten_don_vi }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ item.loai_don_vi }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-action shadow-sm" @click="moModalChiTiet(item)"
                                        title="Xem chi tiết">
                                        <i class="bi bi-eye-fill text-info"></i>
                                    </button>
                                    <button v-if="$hasPermission(36)" class="btn btn-action shadow-sm" @click="moModalSua(item)">
                                        <i class="bi bi-pencil-fill text-primary-darker"></i>
                                    </button>
                                    <button v-if="$hasPermission(36)" class="btn btn-action shadow-sm" @click="xuLyXoa(item.id)">
                                        <i class="bi bi-trash-fill text-danger"></i>
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
                    trong tổng số <b>{{ danhSachLoc.length }}</b> đơn vị
                </span>
                <nav>
                    <ul class="pagination pagination-sm m-0 gap-1">
                        <li class="page-item" :class="{ disabled: trangHienTai === 1 }">
                            <a class="page-link border-0 rounded-circle shadow-sm" href="#"
                                @click.prevent="trangHienTai--">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item" v-for="p in cacTrangHienThi" :key="p"
                            :class="{ active: trangHienTai === p }">
                            <a class="page-link border-0 rounded-circle shadow-sm" href="#"
                                @click.prevent="trangHienTai = p">{{ p }}</a>
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
        <div class="modal fade" id="donViCapModal" tabindex="-1" aria-hidden="true" ref="modalElement">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
                    <div class="modal-header border-0 bg-rose-v2 text-white p-4">
                        <h5 class="modal-title fw-800">{{ laCapNhat ? 'Cập nhật Đơn vị cấp' : 'Thêm Đơn vị cấp mới' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form @submit.prevent="xuLyLuu">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Mã đơn vị</label>
                                    <input type="text" class="form-control flux-input" v-model="duLieuForm.ma_don_vi"
                                        placeholder="Nhập mã đơn vị cấp">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Tên đơn vị</label>
                                    <input type="text" class="form-control flux-input" v-model="duLieuForm.ten_don_vi"
                                        placeholder="Nhập tên đơn vị cấp">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Loại đơn vị</label>
                                    <select class="form-select flux-input" v-model="duLieuForm.loai_don_vi" required>
                                        <option value="khac">Khác</option>
                                        <option value="TRUNG_TAM_TRUC_THUOC">Trung Tâm Trực Thuộc</option>
                                        <option value="DOI_TAC_QUOC_TE">Đối Tác Quốc Tế</option>
                                        <option value="KHOA_VIEN">Khoa Viện</option>
                                    </select>
                                </div>
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
        <div class="modal fade" id="modalChiTietDonViCap" tabindex="-1" aria-hidden="true" ref="modalElementChiTiet">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden">
                    <div class="modal-header border-0 bg-rose-v2 text-white p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-20 rounded-3 p-2">
                                <i class="bi bi-info-circle-fill fs-4"></i>
                            </div>
                            <h5 class="modal-title fw-800 m-0">Chi tiết Đơn vị cấp</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4" v-if="itemDuocChon">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="detail-group">
                                    <label class="small text-uppercase fw-800 text-muted opacity-50 d-block mb-1">Mã định danh đơn vị</label>
                                    <div class="fw-800 fs-4 text-rose-dark">{{ itemDuocChon.ma_don_vi }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-group">
                                    <label class="small text-uppercase fw-800 text-muted opacity-50 d-block mb-1">Tên gọi chính thức</label>
                                    <div class="fw-700 fs-5 text-main">{{ itemDuocChon.ten_don_vi }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-group">
                                    <label class="small text-uppercase fw-800 text-muted opacity-50 d-block mb-1">Phân loại đơn vị</label>
                                    <div class="mt-1">
                                        <span class="badge bg-rose-subtle text-rose-dark border px-3 py-2 rounded-pill">
                                            <i class="bi bi-tag-fill me-1"></i>
                                            {{ getTenLoaiDonVi(itemDuocChon.loai_don_vi) }}
                                        </span>
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
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true" ref="modalElementXoa">
            <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 400px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3">
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="fw-800 text-dark mb-2">Xác nhận xóa?</h4>
                        <p class="text-muted mb-4">Bạn có chắc chắn muốn xóa đơn vị <b>{{ itemXoa?.ten_don_vi }}</b>? Hành động này không thể hoàn tác.</p>
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
    name: "AdminDonViCap",
    data() {
        return {
            danhSach: [],
            dangTai: false,
            dangLuu: false,
            laCapNhat: false,
            tuKhoaTimKiem: "",
            kieuSapXep: 'newest',
            duLieuForm: {
                id: null,
                ma_don_vi: "",
                ten_don_vi: "",
                loai_don_vi: "khac"
            },
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
        if (window.bootstrap) {
            this.instanceModal = new window.bootstrap.Modal(this.$refs.modalElement);
            this.instanceModalChiTiet = new window.bootstrap.Modal(this.$refs.modalElementChiTiet);
            this.instanceModalXoa = new window.bootstrap.Modal(this.$refs.modalElementXoa);
        }
    },
    computed: {
        danhSachLoc() {
            if (!this.tuKhoaTimKiem) return this.danhSach;
            const kw = this.tuKhoaTimKiem.toLowerCase();
            return this.danhSach.filter(item =>
                item.ten_don_vi.toLowerCase().includes(kw) ||
                item.ma_don_vi.toLowerCase().includes(kw)
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
        layDuLieu() {
            this.dangTai = true;
            baseRequestAdmin.get("don-vi-caps/get-data")
                .then(res => {
                    this.danhSach = res.data.data || [];
                    if(this.danhSach) this.danhSach = [...this.danhSach].sort((a, b) => this.kieuSapXep === 'newest' ? b.id - a.id : a.id - b.id);
                })
                .catch(err => {
                    console.error("Lỗi lấy data đơn vị cấp:", err);
                    this.$toast.error("Không thể tải danh sách đơn vị cấp!");
                })
                .finally(() => {
                    this.dangTai = false;
                });
        },
        moModalThem() {
            this.laCapNhat = false;
            this.duLieuForm = {
                id: null,
                ma_don_vi: "",
                ten_don_vi: "",
                loai_don_vi: "khac"
            };
            this.instanceModal.show();
        },
        moModalSua(item) {
            this.laCapNhat = true;
            this.duLieuForm = { ...item };
            this.instanceModal.show();
        },
        moModalChiTiet(item) {
            this.itemDuocChon = item;
            this.instanceModalChiTiet.show();
        },
        xuLyLuu() {
            this.dangLuu = true;
            const request = this.laCapNhat
                ? baseRequestAdmin.put(`don-vi-caps/update/${this.duLieuForm.id}`, this.duLieuForm)
                : baseRequestAdmin.post("don-vi-caps/create", this.duLieuForm);

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
                        this.$toast.error("Lỗi hệ thống khi lưu đơn vị cấp!");
                    }
                })
                .finally(() => {
                    this.dangLuu = false;
                });
        },
        xuLyXoa(id) {
            this.itemXoa = this.danhSach.find(i => i.id === id);
            this.instanceModalXoa.show();
        },
        xacNhanXoa() {
            if (!this.itemXoa) return;
            this.dangTai = true;
            baseRequestAdmin.delete(`don-vi-caps/delete/${this.itemXoa.id}`)
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
                    this.$toast.error("Lỗi khi xóa đơn vị cấp!");
                })
                .finally(() => {
                    this.dangTai = false;
                });
        },
        getTenLoaiDonVi(loai) {
            const map = {
                'TRUNG_TAM_TRUC_THUOC': 'Trung tâm trực thuộc',
                'DOI_TAC_QUOC_TE': 'Đối tác quốc tế',
                'KHOA_VIEN': 'Khoa / Viện',
                'khac': 'Khác'
            };
            return map[loai] || loai;
        }
    }
};
</script>

<style scoped>
.don-vi-cap-management {
    padding: 0;
}

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
