<template>
  <div class="mon-hoc-management">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="page-title">Quản lý Môn Học</h3>
        <p class="page-subtitle">Hệ thống thiết lập chương trình đào tạo và mã môn học nội bộ.</p>
      </div>
      <button v-if="$hasPermission(32)" class="btn-new" @click="moModalThemMoi">
        <i class="bi bi-journal-plus"></i> Thêm môn học
      </button>
    </div>

    <!-- Main Content Card -->
    <div class="data-card shadow-sm border-0">
      <!-- Table Controls -->
      <div class="table-controls d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="navbar-search m-0" style="max-width: 400px; width: 100%;">
          <i class="bi bi-search search-icon"></i>
          <input 
            type="text" 
            v-model="tuKhoaTimKiem" 
            placeholder="Tìm theo tên hoặc mã môn học..." 
          />
        </div>
        <div class="d-flex gap-2">
          <select v-model="locTinChi" class="form-select flux-input" style="width: auto;">
            <option value="">Tất cả tín chỉ</option>
            <option v-for="c in [1, 2, 3, 4, 5]" :key="c" :value="c">{{ c }} tín chỉ</option>
          </select>
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
              <th width="120">Mã môn học</th>
              <th>Tên môn học</th>
              <th class="text-center">Số tín chỉ</th>
              <th>Mô tả</th>
              <th class="text-end pe-3" width="160">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="dangTai">
              <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-rose" role="status">
                  <span class="visually-hidden">Đang tải...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="danhSachPhanTrang.length === 0">
              <td colspan="6" class="text-center py-5">
                <div class="empty-state">
                  <i class="bi bi-inbox d-block mb-2 opacity-25" style="font-size: 3rem"></i>
                  <span class="text-muted">Không tìm thấy môn học nào.</span>
                </div>
              </td>
            </tr>
            <tr v-else v-for="(item, index) in danhSachPhanTrang" :key="item.id">
              <td class="fw-700 text-muted text-center">#{{ (trangHienTai - 1) * soMucMoiTrang + index + 1 }}</td>
              <td class="fw-700 text-rose-dark">{{ item.ma_mon_hoc }}</td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="avatar-sm flex-shrink-0" style="background: rgba(190, 18, 60, 0.08); color: var(--primary-darker);">
                    {{ layKyTuDau(item.ten_mon_hoc) }}
                  </div>
                  <div class="fw-700 text-main">{{ item.ten_mon_hoc }}</div>
                </div>
              </td>
              <td class="text-center fw-700">
                <span class="badge bg-light text-dark border rounded-pill px-3">{{ item.so_tin_chi }}</span>
              </td>
              <td>
                <div class="text-truncate" style="max-width: 250px;" :title="item.mo_ta">
                  {{ item.mo_ta || '---' }}
                </div>
              </td>
              <td class="text-end pe-3">
                <div class="d-flex justify-content-end gap-2">
                  <button class="btn btn-action shadow-sm" @click="moModalChiTiet(item.id)" title="Xem chi tiết">
                    <i class="bi bi-eye-fill text-info"></i>
                  </button>
                  <button v-if="$hasPermission(32)" class="btn btn-action shadow-sm" @click="moModalCapNhat(item)" title="Cập nhật">
                    <i class="bi bi-pencil-fill text-primary-darker"></i>
                  </button>
                  <button v-if="$hasPermission(32)" class="btn btn-action shadow-sm" @click="xuLyXoa(item)" title="Xóa">
                    <i class="bi bi-trash-fill text-danger"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div class="table-footer d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top mt-3" v-if="tongSoTrang > 1">
        <span class="small text-muted mb-3 mb-md-0">
          Hiển thị <b>{{ danhSachPhanTrang.length > 0 ? (trangHienTai - 1) * soMucMoiTrang + 1 : 0 }}</b> 
          - <b>{{ Math.min(trangHienTai * soMucMoiTrang, danhSachLoc.length) }}</b> 
          trong tổng số <b>{{ danhSachLoc.length }}</b> môn học
        </span>
        <nav>
          <ul class="pagination pagination-sm m-0 gap-1">
            <li class="page-item" :class="{ disabled: trangHienTai === 1 }">
              <a class="page-link border-0 rounded-circle" href="#" @click.prevent="trangHienTai--">
                <i class="bi bi-chevron-left"></i>
              </a>
            </li>
            <li 
              class="page-item" 
              v-for="p in cacTrangHienThi" 
              :key="p" 
              :class="{ active: trangHienTai === p }"
            >
              <a class="page-link border-0 rounded-circle shadow-sm" href="#" @click.prevent="trangHienTai = p">{{ p }}</a>
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

    <!-- Subject Modal (Create/Edit) -->
    <div class="modal fade" id="monHocModal" tabindex="-1" aria-hidden="true" ref="modalEle">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
          <div class="modal-header border-0 bg-rose-v2 text-white p-4">
            <h5 class="modal-title fw-800">{{ laCapNhat ? 'Cập nhật môn học' : 'Thêm môn học mới' }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="xuLyLuu">
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label fw-700 small text-uppercase opacity-75">Mã môn học</label>
                  <input type="text" class="form-control flux-input" v-model="duLieuBieuMau.ma_mon_hoc" placeholder="Ví dụ: CS101">
                </div>
                <div class="col-md-8">
                  <label class="form-label fw-700 small text-uppercase opacity-75">Tên môn học</label>
                  <input type="text" class="form-control flux-input" v-model="duLieuBieuMau.ten_mon_hoc" placeholder="Ví dụ: Cấu trúc dữ liệu và Giải thuật">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-700 small text-uppercase opacity-75">Số tín chỉ</label>
                  <input type="number" class="form-control flux-input" v-model="duLieuBieuMau.so_tin_chi" min="1">
                </div>
                <div class="col-12">
                  <label class="form-label fw-700 small text-uppercase opacity-75">Mô tả chi tiết</label>
                  <textarea class="form-control flux-input" v-model="duLieuBieuMau.mo_ta" rows="3" placeholder="Nhập mô tả môn học..."></textarea>
                </div>
              </div>
              
                <div class="mt-4 pt-2 d-flex gap-3">
                  <button type="button" class="btn btn-light border px-4 flex-fill fw-600" data-bs-dismiss="modal">Hủy bỏ</button>
                  <button type="submit" class="btn btn-rose-v2 px-4 flex-fill fw-700 shadow-sm" :disabled="dangLuu">
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
    <div class="modal fade" id="chiTietMonHocModal" tabindex="-1" aria-hidden="true" ref="modalChiTietEle">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
          <div class="modal-header border-0 bg-rose-v2 text-white p-4">
            <h5 class="modal-title fw-800">Chi tiết môn học</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-0">
            <div v-if="duLieuChiTiet">
              <div class="p-4 border-bottom bg-light-subtle">
                <div class="d-flex align-items-center gap-3">
                  <div class="bg-rose-v2 text-white rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px;">
                    <i class="bi bi-journals fs-3"></i>
                  </div>
                  <div>
                    <h4 class="fw-800 text-dark mb-1">{{ duLieuChiTiet.ten_mon_hoc }}</h4>
                    <span class="badge bg-rose-subtle text-dark rounded-pill px-3">{{ duLieuChiTiet.ma_mon_hoc }}</span>
                  </div>
                </div>
              </div>
              <div class="p-4">
                <div class="row g-3">
                  <div class="col-sm-6">
                    <div class="d-flex align-items-center p-3 border rounded-3 bg-white h-100 shadow-sm">
                      <div class="flex-shrink-0 me-3 bg-rose-subtle text-rose rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-hash fs-5"></i>
                      </div>
                      <div>
                        <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Mã môn học</div>
                        <div class="fw-bold text-dark mb-0 fs-6">{{ duLieuChiTiet.ma_mon_hoc }}</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="d-flex align-items-center p-3 border rounded-3 bg-white h-100 shadow-sm">
                      <div class="flex-shrink-0 me-3 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-award fs-5"></i>
                      </div>
                      <div>
                        <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Số tín chỉ</div>
                        <div class="fw-bold text-dark mb-0 fs-6">{{ duLieuChiTiet.so_tin_chi }}</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="p-3 border rounded-3 bg-white h-100 shadow-sm">
                      <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-card-text text-rose me-2"></i>
                        <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Mô tả học phần</div>
                      </div>
                      <div class="fw-bold text-dark mb-0 fs-6 text-wrap" style="line-height: 1.5;">{{ duLieuChiTiet.mo_ta || 'Không có mô tả' }}</div>
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
            <button type="button" class="btn btn-light border px-4 pb-2 pt-2 fw-600" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteMonHocModal" tabindex="-1" aria-hidden="true" ref="modalElementXoa">
        <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-800 text-dark mb-2">Xác nhận xóa?</h4>
                    <p class="text-muted mb-4">Bạn có chắc chắn muốn xóa môn học <b>{{ itemXoa?.ten_mon_hoc }}</b> ({{ itemXoa?.ma_mon_hoc }})? Hành động này không thể hoàn tác.</p>
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
  name: "AdminMonHoc",
  data() {
    return {
      danhSach: [],
      dangTai: false,
      dangLuu: false,
      tuKhoaTimKiem: "",
            kieuSapXep: 'newest',
      locTinChi: "",
      laCapNhat: false,
      trangHienTai: 1,
      soMucMoiTrang: 10,
      duLieuBieuMau: {
        id: null,
        ma_mon_hoc: "",
        ten_mon_hoc: "",
        so_tin_chi: 3,
        mo_ta: ""
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
        ketQua = ketQua.filter(item => 
          item.ma_mon_hoc.toLowerCase().includes(kw) || 
          item.ten_mon_hoc.toLowerCase().includes(kw)
        );
      }
      
      if (this.locTinChi) {
        ketQua = ketQua.filter(item => parseInt(item.so_tin_chi) === parseInt(this.locTinChi));
      }
      
      
            return ketQua;
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
        kieuSapXep() {
            if(this.danhSach) this.danhSach = [...this.danhSach].sort((a, b) => this.kieuSapXep === 'newest' ? b.id - a.id : a.id - b.id);
        },
        
    tuKhoaTimKiem() { this.trangHienTai = 1; },
    locTinChi() { this.trangHienTai = 1; }
  },
  mounted() {
    this.layDuLieu();
    if (window.bootstrap) {
      this.modal = new window.bootstrap.Modal(this.$refs.modalEle);
      this.modalChiTiet = new window.bootstrap.Modal(this.$refs.modalChiTietEle);
      this.instanceModalXoa = new window.bootstrap.Modal(this.$refs.modalElementXoa);
    }
  },
  methods: {
    layDuLieu() {
      this.dangTai = true;
      baseRequestAdmin.get("mon-hocs/get-data")
        .then(res => {
          const data = res.data.list || res.data.data || res.data || [];
          this.danhSach = Array.isArray(data) ? data : [];
        })
        .catch(err => {
          console.error("Lỗi lấy data môn học:", err);
        })
        .finally(() => {
          this.dangTai = false;
        });
    },
    moModalThemMoi() {
      this.laCapNhat = false;
      this.duLieuBieuMau = { id: null, ma_mon_hoc: "", ten_mon_hoc: "", so_tin_chi: 3, mo_ta: "" };
      this.modal.show();
    },
    moModalCapNhat(item) {
      this.laCapNhat = true;
      this.duLieuBieuMau = { ...item };
      this.modal.show();
    },
    moModalChiTiet(id) {
      this.duLieuChiTiet = null;
      this.modalChiTiet.show();
      baseRequestAdmin.get(`mon-hocs/detail/${id}`)
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
        ? baseRequestAdmin.put(`mon-hocs/update/${this.duLieuBieuMau.id}`, this.duLieuBieuMau)
        : baseRequestAdmin.post("mon-hocs/create", this.duLieuBieuMau);

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
      baseRequestAdmin.delete(`mon-hocs/delete/${this.itemXoa.id}`)
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
            this.$toast.error("Lỗi hệ thống khi xóa môn học!");
          }
        })
        .finally(() => {
          this.dangTai = false;
        });
    },
    layKyTuDau(name) {
      if (!name) return "??";
      return name.split(' ').map(n => n[0]).join('').slice(-2).toUpperCase();
    }
  }
};
</script>
