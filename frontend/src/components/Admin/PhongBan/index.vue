<template>
    <div class="phong-ban-management">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title">Quản lý Phòng Ban</h3>
                <p class="page-subtitle">Hệ thống quản trị cơ cấu tổ chức và các khoa, phòng ban nội bộ.</p>
            </div>
            <button v-if="$hasPermission(22)" class="btn-new" @click="moModalThem">
                <i class="bi bi-building-add"></i> Thêm phòng ban
            </button>
        </div>

        <!-- Main Content Card -->
        <div class="data-card shadow-sm border-0">
            <!-- Table Controls -->
            <div class="table-controls d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="navbar-search m-0" style="max-width: 400px; width: 100%;">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" v-model="tuKhoaTimKiem" placeholder="Tìm theo mã hoặc tên phòng ban..." />
                </div>
                <div class="d-flex gap-2">
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
                            <th width="80" class="text-center">STT</th>
                            <th width="150">Mã Phòng Ban</th>
                            <th>Tên Phòng Ban</th>
                            <th>Mô tả chi tiết</th>
                            <th class="text-end" width="120">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="dangTai">
                            <td colspan="5" class="text-center py-5">
                                <div class="spinner-border text-rose" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="danhSachPhanTrang.length === 0">
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-building d-block mb-2 opacity-25" style="font-size: 3rem"></i>
                                    <span class="text-muted">Không tìm thấy phòng ban nào.</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else v-for="(phongBan, index) in danhSachPhanTrang" :key="phongBan.id">
                            <td class="fw-700 text-muted text-center">#{{ (trangHienTai - 1) * soMucMoiTrang + index + 1
                                }}</td>
                            <td class="fw-700 text-rose-dark">{{ phongBan.ma_phong_ban }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm flex-shrink-0 me-3"
                                        style="background: rgba(190, 18, 60, 0.08); color: var(--primary-darker);">
                                        {{ layKyTuDau(phongBan.ten_phong_ban) }}
                                    </div>
                                    <div class="fw-700 text-main">{{ phongBan.ten_phong_ban }}</div>
                                </div>
                            </td>
                            <td class="small text-muted text-wrap" style="max-width: 300px; line-height: 1.5;">
                                {{ phongBan.mo_ta || '---' }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button v-if="$hasPermission(22)" class="btn btn-action shadow-sm" @click="moModalSua(phongBan)">
                                        <i class="bi bi-pencil-fill text-primary-darker"></i>
                                    </button>
                                    <button v-if="$hasPermission(22)" class="btn btn-action shadow-sm" @click="xuLyXoa(phongBan)" title="Xóa">
                                        <i class="bi bi-trash-fill text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div
                class="table-footer d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top mt-3" v-if="tongSoTrang > 1">
                <span class="small text-muted mb-3 mb-md-0">
                    Hiển thị <b>{{ danhSachPhanTrang.length > 0 ? (trangHienTai - 1) * soMucMoiTrang + 1 : 0 }}</b>
                    - <b>{{ Math.min(trangHienTai * soMucMoiTrang, danhSachLoc.length) }}</b>
                    trong tổng số <b>{{ danhSachLoc.length }}</b> phòng ban
                </span>
                <nav >
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
        <div class="modal fade" id="phongBanModal" tabindex="-1" aria-hidden="true" ref="modalEle">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
                    <div class="modal-header border-0 bg-rose-v2 text-white p-4">
                        <h5 class="modal-title fw-800">{{ laChinhSua ? 'Cập nhật phòng ban' : 'Thêm phòng ban mới' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form @submit.prevent="xuLyLuu">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Mã Phòng
                                        Ban</label>
                                    <input type="text" class="form-control flux-input"
                                        v-model="duLieuBieuMau.ma_phong_ban"
                                        placeholder="Ví dụ: PB_DAOTAO, KHOA_CNTT...">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Tên Phòng
                                        Ban</label>
                                    <input type="text" class="form-control flux-input"
                                        v-model="duLieuBieuMau.ten_phong_ban"
                                        placeholder="Ví dụ: Khoa Công nghệ Thông tin">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Mô tả/Chức
                                        năng</label>
                                    <textarea class="form-control flux-input" v-model="duLieuBieuMau.mo_ta" rows="3"
                                        placeholder="Ghi chú thêm về nhiệm vụ của phòng ban..."></textarea>
                                </div>
                            </div>

                            <div class="mt-4 pt-2 d-flex gap-3">
                                <button type="button" class="btn btn-light border px-4 flex-fill fw-600"
                                    data-bs-dismiss="modal">Hủy bỏ</button>
                                <button type="submit" class="btn btn-rose-v2 px-4 flex-fill fw-700 shadow-sm"
                                    :disabled="dangLuu">
                                    <span v-if="dangLuu" class="spinner-border spinner-border-sm me-1"></span>
                                    {{ laChinhSua ? 'Cập nhật' : 'Xác nhận tạo' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deletePhongBanModal" tabindex="-1" aria-hidden="true" ref="modalElementXoa">
            <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 400px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3">
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="fw-800 text-dark mb-2">Xác nhận xóa?</h4>
                        <p class="text-muted mb-4">Bạn có chắc chắn muốn xóa phòng ban <b>{{ itemXoa?.ten_phong_ban }}</b>? Hành động này không thể hoàn tác.</p>
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
    name: "AdminPhongBan",
    data() {
        return {
            danhSach: [],
            dangTai: false,
            dangLuu: false,
            tuKhoaTimKiem: "",
            laChinhSua: false,
            trangHienTai: 1,
            soMucMoiTrang: 10,
            duLieuBieuMau: {
                id: null,
                ma_phong_ban: "",
                ten_phong_ban: "",
                mo_ta: ""
            },
            itemXoa: null,
            modalPhongBan: null,
            instanceModalXoa: null
        };
    },
    computed: {
        danhSachLoc() {
            if (!this.tuKhoaTimKiem.trim()) return this.danhSach;
            const kw = this.tuKhoaTimKiem.toLowerCase();
            return this.danhSach.filter(item =>
                (item.ten_phong_ban || "").toLowerCase().includes(kw) ||
                (item.ma_phong_ban || "").toLowerCase().includes(kw)
            );
        },
        danhSachPhanTrang() {
            const batDau = (this.trangHienTai - 1) * this.soMucMoiTrang;
            return this.danhSachLoc.slice(batDau, batDau + this.soMucMoiTrang);
        },
        tongSoTrang() {
            return Math.ceil(this.danhSachLoc.length / this.soMucMoiTrang);
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
        tuKhoaTimKiem() {
            this.trangHienTai = 1;
        }
    },
    mounted() {
        this.layDuLieu();
        if (window.bootstrap) {
            this.modalPhongBan = new window.bootstrap.Modal(this.$refs.modalEle);
            this.instanceModalXoa = new window.bootstrap.Modal(this.$refs.modalElementXoa);
        }
    },
    methods: {
        layKyTuDau(ten) {
            if (!ten) return "??";
            return ten.split(' ').map(n => n[0]).join('').slice(-2).toUpperCase();
        },
        layDuLieu() {
            this.dangTai = true;
            baseRequestAdmin.get("phong-bans/get-data")
                .then(res => {
                    this.danhSach = res.data.list || res.data.data || res.data;
                })
                .catch(err => {
                    console.error("Lỗi lấy data phòng ban:", err);
                })
                .finally(() => {
                    this.dangTai = false;
                });
        },
        moModalThem() {
            this.laChinhSua = false;
            this.duLieuBieuMau = { id: null, ma_phong_ban: "", ten_phong_ban: "", mo_ta: "" };
            this.modalPhongBan.show();
        },
        moModalSua(phongBan) {
            this.laChinhSua = true;
            this.duLieuBieuMau = { ...phongBan };
            this.modalPhongBan.show();
            baseRequestAdmin.get(`phong-bans/detail/${phongBan.id}`)
                .then(res => {
                    if (res.data.data) {
                        this.duLieuBieuMau = { ...res.data.data };
                    }
                })
                .catch(err => {
                    console.error(err);
                });
        },
        xuLyLuu() {
            this.dangLuu = true;
            const yeuCau = this.laChinhSua
                ? baseRequestAdmin.put(`phong-bans/update/${this.duLieuBieuMau.id}`, this.duLieuBieuMau)
                : baseRequestAdmin.post("phong-bans/create", this.duLieuBieuMau);

            yeuCau
                .then((res) => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.modalPhongBan.hide();
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
        xuLyXoa(phongBan) {
            this.itemXoa = phongBan;
            this.instanceModalXoa.show();
        },
        xacNhanXoa() {
            if (!this.itemXoa) return;
            this.dangTai = true;
            baseRequestAdmin.delete(`phong-bans/delete/${this.itemXoa.id}`)
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
                        this.$toast.error("Lỗi hệ thống khi xóa phòng ban!");
                    }
                })
                .finally(() => {
                    this.dangTai = false;
                });
        }
    }
};
</script>

<style scoped>
.btn-rose-v2 {
    background: var(--primary-darker, #BE123C);
    color: #fff;
}

.bg-rose-v2 {
    background: linear-gradient(135deg, var(--primary-darker, #9F1239), #BE123C);
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
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-action:hover {
    transform: translateY(-2px);
    background: var(--primary, #f3f4f6);
}

.flux-input {
    border-radius: 10px;
    border: 1px solid var(--border-color, #e5e7eb);
    padding: 10px 14px;
    font-size: 13.5px;
}

.flux-input:focus {
    border-color: var(--primary-darker, #BE123C);
    box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.1);
}

.avatar-sm {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.85rem;
    box-shadow: 0 2px 8px rgba(190, 18, 60, 0.1);
}

.text-rose-dark {
    color: var(--primary-darker, #9F1239);
}

.text-wrap {
    white-space: normal;
    word-break: break-word;
}

.fw-800 {
    font-weight: 800;
}

.fw-700 {
    font-weight: 700;
}

.fw-600 {
    font-weight: 600;
}

.pagination .page-link {
    color: #BE123C;
    border: none;
    background: #fff;
}

.pagination .page-item.active .page-link {
    background: #BE123C;
    color: #fff;
    border-radius: 50% !important;
}
</style>
