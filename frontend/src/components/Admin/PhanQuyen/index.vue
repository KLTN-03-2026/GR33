<template>
  <div class="phan-quyen-management">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="page-title">Quản lý Chức Vụ & Phân Quyền</h3>
        <p class="page-subtitle">Quản lý danh sách chức vụ và phân bổ quyền truy cập hệ thống.</p>
      </div>
      <button v-if="$hasPermission(12)" class="btn-new" @click="moModalThemChucVu">
        <i class="bi bi-shield-plus"></i> Thêm chức vụ
      </button>
    </div>

    <div class="row">
      <!-- CỘT 1: Danh sách chức vụ -->
      <div class="col-lg-4 col-md-5">
        <div class="card shared-card border-0 shadow-sm h-100 p-0 overflow-hidden">
          <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <div
                class="bg-rose-v2 text-white fw-700 d-flex align-items-center justify-content-center rounded-circle me-2"
                style="width: 28px; height: 28px; font-size: 14px;">
                1
              </div>
              <h6 class="mb-0 fw-800 text-uppercase tracking-wider opacity-75 small">Chọn Chức Vụ</h6>
            </div>
          </div>
          <div class="card-body p-0 card-body-scroll bg-white">
            <div v-for="chucVu in danhSachChucVu" :key="chucVu.id" class="role-item p-3 border-bottom cursor-pointer transition-all"
              :class="{ 'active-role shadow-sm': idChucVuDaChon === chucVu.id }" @click="chonChucVu(chucVu.id)">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="fw-800 fs-6 pe-2" :class="idChucVuDaChon === chucVu.id ? 'text-rose-dark' : 'text-dark'">{{
                  chucVu.ten_chuc_vu }}</div>
                
                <div class="d-flex gap-2 align-items-center role-actions transition-all opacity-100">
                  <button v-if="$hasPermission(13)" class="btn btn-action shadow-sm" @click.stop="moModalSuaChucVu(chucVu)"
                    :disabled="chucVu.ten_chuc_vu === 'Super Admin'" title="Sửa">
                    <i class="bi bi-pencil-fill text-primary-darker"></i>
                  </button>
                  <button v-if="$hasPermission(13)" class="btn btn-action shadow-sm" @click.stop="xacNhanXoaChucVu(chucVu)"
                    :disabled="chucVu.ten_chuc_vu === 'Super Admin'" title="Xóa">
                    <i class="bi bi-trash-fill text-danger"></i>
                  </button>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <span class="badge rounded-pill fw-700 py-1 px-3 border shadow-sm transition-all status-badge"
                  :class="[
                      chucVu.trang_thai === 1 ? (idChucVuDaChon === chucVu.id ? 'bg-white text-rose' : 'bg-success bg-opacity-10 text-success border-success-subtle') : 'bg-light text-muted',
                      $hasPermission(13) ? 'cursor-pointer' : 'opacity-75'
                  ]"
                  @click.stop="$hasPermission(13) ? doiTrangThai(chucVu) : null">
                  {{ chucVu.trang_thai === 1 ? 'Đang hoạt động' : 'Tạm ngừng' }}
                </span>
                
                <div v-if="chucVu.ten_chuc_vu === 'Super Admin'" class="small opacity-50 text-uppercase fw-700" style="font-size: 10px;">
                  Hệ thống
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CỘT 2: Quyền đã gán -->
      <div class="col-lg-4 col-md-7">
        <div class="card shared-card border-0 shadow-sm h-100 p-0 overflow-hidden">
          <div class="card-header bg-white border-bottom py-3 d-flex align-items-center">
            <div class="bg-rose-v2 text-white fw-700 d-flex align-items-center justify-content-center rounded-circle me-2"
              style="width: 28px; height: 28px; font-size: 14px;">
              2
            </div>
            <h6 class="mb-0 fw-800 text-uppercase tracking-wider opacity-75 small">Quyền của "{{ tenChucVuHienTai }}"</h6>
          </div>
          <div class="card-body p-0 card-body-scroll bg-light-subtle">
            <div v-if="!idChucVuDaChon"
              class="h-100 d-flex flex-column align-items-center justify-content-center text-muted p-5 text-center">
              <i class="bi bi-shield-lock mb-3 opacity-25" style="font-size: 4rem;"></i>
              <p class="fw-700 mb-1">Chưa chọn chức vụ</p>
              <p class="small opacity-75">Vui lòng chọn một chức vụ ở cột bên trái.</p>
            </div>
            <div v-else-if="danhSachPhanQuyen.length === 0"
              class="h-100 d-flex flex-column align-items-center justify-content-center text-muted p-5 text-center">
              <i class="bi bi-shield-slash mb-3 opacity-25" style="font-size: 4rem;"></i>
              <p class="fw-700 mb-1">Chưa có phân quyền</p>
              <p class="small opacity-75">Hãy cấp quyền cho chức vụ này từ cột bên phải.</p>
            </div>
            <ul v-else class="list-group list-group-flush pb-3">
              <li v-for="phanQuyen in danhSachPhanQuyen" :key="phanQuyen.id"
                class="list-group-item d-flex justify-content-between align-items-start p-3 mx-3 mt-3 rounded-4 shadow-sm border border-light bg-white scale-hover min-h-item">
                <div class="d-flex align-items-start min-w-0 flex-grow-1">
                  <div class="icon-wrap bg-success-v2 text-success rounded-circle me-3 flex-shrink-0 mt-1"
                    style="width: 36px; height: 36px; font-size: 1.1rem;">
                    <i class="bi bi-check2-circle"></i>
                  </div>
                  <div class="flex-grow-1 pe-2">
                    <div class="fw-800 text-dark mb-0 lh-sm" :title="layTenChucNang(phanQuyen.chuc_nang_id)">
                      {{ layTenChucNang(phanQuyen.chuc_nang_id) }}
                    </div>
                    <div class="small fw-700 text-muted opacity-75 mt-1">Ref ID: <span class="text-rose-dark">#{{ phanQuyen.id }}</span></div>
                  </div>
                </div>
                <button v-if="$hasPermission(13)" class="btn btn-action shadow-sm ms-2 flex-shrink-0" @click="xacNhanXoaQuyen(phanQuyen)"
                  :disabled="dangXuLy === phanQuyen.id || tenChucVuHienTai === 'Super Admin' || trangThaiChucVuDaChon === 1" 
                  :title="trangThaiChucVuDaChon === 1 ? 'Vui lòng tạm ngừng chức vụ trước khi gỡ quyền' : 'Gỡ bỏ phân quyền này'">
                  <span v-if="dangXuLy === phanQuyen.id" class="spinner-border spinner-border-sm mx-1"></span>
                  <i v-else class="bi bi-trash-fill text-danger fs-6"></i>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- CỘT 3: Kho Chức Năng Hệ Thống -->
      <div class="col-lg-4 col-md-12">
        <div class="card shared-card border-0 shadow-sm h-100 p-0 overflow-hidden">
          <div class="card-header bg-white border-bottom py-3 d-flex align-items-center">
            <div class="bg-rose-v2 text-white fw-700 d-flex align-items-center justify-content-center rounded-circle me-2"
              style="width: 28px; height: 28px; font-size: 14px;">
              3
            </div>
            <h6 class="mb-0 fw-800 text-uppercase tracking-wider opacity-75 small">Kho Chức năng Hệ thống</h6>
          </div>
          <div class="p-3 bg-light border-bottom">
            <div class="navbar-search m-0 w-100 shadow-sm"
              style="border-radius: 12px; overflow: hidden; background: #fff;">
              <i class="bi bi-search search-icon ms-3"></i>
              <input type="text" class="form-control border-0 bg-transparent ps-5 fw-600" style="box-shadow: none;"
                v-model="tuKhoaTimKiemChucNang" placeholder="Tra cứu tìm phân quyền..." />
            </div>
          </div>
          <div class="card-body p-0 card-body-scroll bg-white">
            <ul class="list-group list-group-flush pb-3">
              <li v-for="chucNang in danhSachChucNangLoc" :key="chucNang.id"
                class="list-group-item d-flex flex-column p-4 border-bottom-0 mx-2 mt-2 rounded-4 role-item-light border"
                :class="daPhanQuyen(chucNang.id) ? 'bg-light-success border-success-subtle' : 'border-light'">
                <div class="mb-2 d-flex align-items-center">
                  <i class="bi fs-5 flex-shrink-0 me-3 fw-bold"
                    :class="daPhanQuyen(chucNang.id) ? 'bi-shield-check text-success' : 'bi-shield text-muted opacity-50'"></i>
                  <span class="fw-800 fs-6" :class="daPhanQuyen(chucNang.id) ? 'text-success hover-text-dark' : 'text-dark'">{{
                    chucNang.ten_chuc_nang }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2 ps-1">
                  <span class="badge bg-light text-muted fw-700 rounded-pill px-3 py-2 shadow-sm border">ID FN: {{ chucNang.id
                    }}</span>

                  <button v-if="daPhanQuyen(chucNang.id)"
                    class="btn btn-sm btn-white border text-success fw-800 rounded-pill px-4 shadow-sm d-flex align-items-center"
                     @click="$hasPermission(13) ? xuLyGoPhanQuyenTheoChucNang(chucNang.id) : null"
                     :disabled="trangThaiChucVuDaChon === 1 || !$hasPermission(13)"
                     :title="trangThaiChucVuDaChon === 1 ? 'Vui lòng tạm ngừng chức vụ để gỡ quyền' : ($hasPermission(13) ? 'Click để gỡ quyền' : 'Bạn không có quyền này')">
                    <i class="bi bi-check2-all me-1 fs-6"></i> Đã cấp quyền
                  </button>

                  <button v-else
                    class="btn btn-sm btn-rose-v2 fw-800 rounded-pill px-4 shadow-sm d-flex align-items-center transition-scale"
                    :disabled="!idChucVuDaChon || dangXuLy === 'add_' + chucNang.id || trangThaiChucVuDaChon === 1 || !$hasPermission(13)" 
                    @click="$hasPermission(13) ? xuLyPhanQuyen(chucNang.id) : null"
                    :class="{ 'opacity-25 grayscale': !idChucVuDaChon || trangThaiChucVuDaChon === 1 || !$hasPermission(13) }"
                    :title="!idChucVuDaChon ? 'Vui lòng chọn chức vụ ở cột 1 trước' : (trangThaiChucVuDaChon === 1 ? 'Vui lòng tạm ngừng chức vụ để cấp quyền' : ($hasPermission(13) ? 'Click để cấp quyền này cho chức vụ' : 'Bạn không có quyền thao tác'))">
                    <span v-if="dangXuLy === 'add_' + chucNang.id" class="spinner-border spinner-border-sm me-2"></span>
                    <i v-else class="bi bi-plus-lg me-1 fs-6"></i> Cấp ngay
                  </button>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals (Thêm/Sửa) -->
    <div class="modal fade" id="modalChucVu" tabindex="-1" aria-hidden="true" ref="modalChucVuEle">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="modal-header border-0 bg-rose-v2 text-white p-4">
            <h5 class="modal-title fw-800">{{ laChinhSuaChucVu ? 'Cập nhật Chức vụ' : 'Thêm Chức vụ mới' }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="luuChucVu">
              <div class="mb-4">
                <label class="form-label fw-800 small text-uppercase opacity-75 mb-2">Tên Chức Vụ <span
                    class="text-danger">*</span></label>
                <input type="text" class="form-control flux-input fw-700" v-model="duLieuChucVu.ten_chuc_vu" 
                  placeholder="Ví dụ: Giám đốc, Trưởng phòng..." :readonly="dangLuuChucVu">
              </div>
              <div class="d-flex gap-3 mt-4">
                <button type="button" class="btn btn-light border px-4 flex-fill fw-700" data-bs-dismiss="modal">Hủy
                  bỏ</button>
                <button type="submit" class="btn btn-rose-v2 px-4 flex-fill fw-800 shadow-sm" :disabled="dangLuuChucVu">
                  <span v-if="dangLuuChucVu" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-save me-1"></i> {{ laChinhSuaChucVu ? 'Cập nhật' : 'Tạo chức vụ' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="modalXoa" tabindex="-1" aria-hidden="true" ref="modalXoaEle">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="modal-header bg-danger text-white border-0 py-3">
            <h5 class="modal-title fw-800 mb-0 d-flex align-items-center">
              <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
              Xác nhận xóa
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4 text-center">
            <p class="mb-0 fw-600 text-dark">{{ thongBaoXoa }}</p>
            <p class="text-muted small mt-2 mb-0">Hành động này không thể hoàn tác!</p>
          </div>
          <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-light border px-4 py-2 fw-700 flex-fill"
              data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-danger px-4 py-2 fw-800 flex-fill shadow-sm" @click="thucHienXoa"
              :disabled="dangThucHienXoa">
              <span v-if="dangThucHienXoa" class="spinner-border spinner-border-sm me-1"></span>
              Xóa ngay
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import baseRequestAdmin from "../../../core/baseRequestAdmin";

export default {
  name: "AdminPhanQuyen",
  data() {
    return {
      danhSachChucVu: [],
      danhSachChucNang: [],
      danhSachPhanQuyen: [],
      idChucVuDaChon: null,
      tuKhoaTimKiemChucNang: "",
      dangXuLy: null,

      // Trạng thái Modal Chức vụ
      duLieuChucVu: { id: null, ten_chuc_vu: "" },
      laChinhSuaChucVu: false,
      dangLuuChucVu: false,
      modalChucVu: null,

      // Trạng thái Modal Xóa
      modalXoa: null,
      loaiXoa: "", // "role" or "permission"
      doiTuongXoa: null,
      thongBaoXoa: "",
      dangThucHienXoa: false
    };
  },
  computed: {
    tenChucVuHienTai() {
      const chucVu = this.danhSachChucVu.find(r => r.id === this.idChucVuDaChon);
      return chucVu ? chucVu.ten_chuc_vu : "Chưa chọn";
    },
    trangThaiChucVuDaChon() {
      const chucVu = this.danhSachChucVu.find(r => r.id === this.idChucVuDaChon);
      return chucVu ? chucVu.trang_thai : null;
    },
    danhSachChucNangLoc() {
      if (!this.tuKhoaTimKiemChucNang) return this.danhSachChucNang;
      return this.danhSachChucNang.filter(fn =>
        fn.ten_chuc_nang.toLowerCase().includes(this.tuKhoaTimKiemChucNang.toLowerCase())
      );
    }
  },
  mounted() {
    this.layTatCaDuLieu();
    if (window.bootstrap) {
      this.modalChucVu = new window.bootstrap.Modal(this.$refs.modalChucVuEle);
      this.modalXoa = new window.bootstrap.Modal(this.$refs.modalXoaEle);
    }
  },
  methods: {
    layTatCaDuLieu() {
      baseRequestAdmin.get("chuc-vus/get-data")
        .then(resChucVu => {
          this.danhSachChucVu = resChucVu.data.data || resChucVu.data.list || [];
          return baseRequestAdmin.get("chuc-nang/get-data");
        })
        .then(resChucNang => {
          this.danhSachChucNang = resChucNang.data.data || resChucNang.data.list || [];
        })
        .catch(err => {
          console.error("Lỗi lấy dữ liệu:", err);
        });
    },
    chonChucVu(idChucVu) {
      this.idChucVuDaChon = idChucVu;
      baseRequestAdmin.get("phan-quyen/get-data")
        .then(res => {
          const tatCaDuLieu = res.data.data || res.data.list || [];
          this.danhSachPhanQuyen = tatCaDuLieu.filter(p => p.chuc_vu_id === idChucVu);
        })
        .catch(err => {
          console.error(err);
        });
    },
    daPhanQuyen(idChucNang) {
      return this.danhSachPhanQuyen.some(pq => pq.chuc_nang_id === idChucNang);
    },
    layTenChucNang(idChucNang) {
      const fn = this.danhSachChucNang.find(f => f.id === idChucNang);
      return fn ? fn.ten_chuc_nang : `Quyền #${idChucNang}`;
    },
    xuLyPhanQuyen(idChucNang) {
      if (!this.idChucVuDaChon || this.trangThaiChucVuDaChon === 1) return;
      this.dangXuLy = "add_" + idChucNang;
      baseRequestAdmin.post("phan-quyen/create", {
        chuc_vu_id: this.idChucVuDaChon,
        chuc_nang_id: idChucNang
      })
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            this.chonChucVu(this.idChucVuDaChon);
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
          this.dangXuLy = null;
        });
    },
    xuLyGoPhanQuyenTheoChucNang(idChucNang) {
      const pq = this.danhSachPhanQuyen.find(p => p.chuc_nang_id === idChucNang);
      if (pq) {
        this.xacNhanXoaQuyen(pq);
      }
    },
    doiTrangThai(chucVu) {
      if (chucVu.ten_chuc_vu === 'Super Admin') return;

      const trangThaiMoi = chucVu.trang_thai === 1 ? 0 : 1;
      baseRequestAdmin.post("chuc-vus/change-status", {
        id: chucVu.id,
        trang_thai: trangThaiMoi
      })
        .then(res => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            chucVu.trang_thai = trangThaiMoi;
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

    // Các phương thức CRUD Chức vụ
    moModalThemChucVu() {
      this.laChinhSuaChucVu = false;
      this.duLieuChucVu = { id: null, ten_chuc_vu: "" };
      this.modalChucVu.show();
    },
    moModalSuaChucVu(chucVu) {
      this.laChinhSuaChucVu = true;
      this.duLieuChucVu = { ...chucVu };
      this.modalChucVu.show();
    },
    luuChucVu() {
      this.dangLuuChucVu = true;
      const yeuCau = this.laChinhSuaChucVu 
        ? baseRequestAdmin.put(`chuc-vus/update/${this.duLieuChucVu.id}`, this.duLieuChucVu)
        : baseRequestAdmin.post("chuc-vus/create", this.duLieuChucVu);

      yeuCau
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            this.modalChucVu.hide();
            this.layTatCaDuLieu();
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
          this.dangLuuChucVu = false;
        });
    },

    // Xử lý Xóa tập trung
    xacNhanXoaQuyen(pq) {
      if (this.trangThaiChucVuDaChon === 1) return;
      this.loaiXoa = "permission";
      this.doiTuongXoa = pq;
      this.thongBaoXoa = `Xác nhận gỡ bỏ quyền "${this.layTenChucNang(pq.chuc_nang_id)}"?`;
      this.modalXoa.show();
    },
    xacNhanXoaChucVu(chucVu) {
      this.loaiXoa = "role";
      this.doiTuongXoa = chucVu;
      this.thongBaoXoa = `Xóa vĩnh viễn chức vụ "${chucVu.ten_chuc_vu}"?`;
      this.modalXoa.show();
    },
    thucHienXoa() {
      if (!this.doiTuongXoa) return;
      this.dangThucHienXoa = true;
      const yeuCau = this.loaiXoa === "permission"
        ? baseRequestAdmin.post(`phan-quyen/delete`, { id: this.doiTuongXoa.id })
        : baseRequestAdmin.delete(`chuc-vus/delete/${this.doiTuongXoa.id}`);

      yeuCau
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            if (this.loaiXoa === "permission") {
              this.chonChucVu(this.idChucVuDaChon);
            } else if (this.loaiXoa === "role") {
              this.layTatCaDuLieu();
              if (this.idChucVuDaChon === this.doiTuongXoa.id) {
                this.idChucVuDaChon = null;
                this.danhSachPhanQuyen = [];
              }
            }
            this.modalXoa.hide();
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
          this.dangThucHienXoa = false;
          this.doiTuongXoa = null;
        });
    }
  }
};
</script>

<style scoped>
.shared-card {
  border-radius: 20px;
}

.card-body-scroll {
  height: calc(100vh - 280px);
  overflow-y: auto;
}

.card-body-scroll::-webkit-scrollbar {
  width: 4px;
}

.card-body-scroll::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}

.role-item {
  border-left: 4px solid transparent;
  transition: all 0.2s ease;
}

.role-item:hover {
  background: var(--primary) !important;
}
.role-item:hover .text-dark, 
.role-item:hover .text-muted {
  color: var(--primary-text) !important;
}

.active-role {
  background: var(--primary) !important;
  color: var(--primary-text) !important;
  font-weight: 600;
  box-shadow: inset 3px 0 0 var(--primary-darker) !important;
  border-left: 0 !important;
}

.active-role .badge {
  background: #fff !important;
  color: var(--primary-darker) !important;
}

.status-badge {
  transition: all 0.2s;
}
.status-badge:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}

.role-actions button {
  width: 32px;
  height: 32px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.82);
  border: 1px solid #eee;
}

.min-h-item {
  min-height: 80px;
}

.role-item-light {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid #f1f5f9 !important;
}

.role-item-light:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
  border-color: #e2e8f0 !important;
}

.bg-light-success {
  background-color: rgba(22, 163, 74, 0.05);
}

.flux-input {
  border-radius: 12px;
  border: 1.5px solid #edf2f7;
  padding: 12px 16px;
  transition: 0.2s;
}

.flux-input:focus {
  border-color: var(--primary-darker);
  box-shadow: 0 0 0 4px rgba(190, 18, 60, 0.1);
  outline: none;
}

.form-switch-rose .form-check-input:checked {
  background-color: var(--primary-darker);
  border-color: var(--primary-darker);
}

.transition-all {
  transition: all 0.25s ease;
}

.scale-hover:hover {
  transform: translateY(-2px);
}

.transition-scale {
  transition: 0.2s ease;
}

.transition-scale:hover {
  transform: scale(1.02);
}

.grayscale {
  filter: grayscale(1);
}

@media (max-width: 991.98px) {
  .card-body-scroll {
    height: 400px;
  }
}
</style>
