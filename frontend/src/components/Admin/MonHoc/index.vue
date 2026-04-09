<template>
  <div class="mon-hoc-management">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="page-title text-main fw-800">Quản Lý Môn Học</h3>
        <p class="page-subtitle text-muted">Hệ thống quản lý môn học và số tín chỉ</p>
      </div>
      <button class="btn-new shadow-sm" @click="openCreateModal">
        <i class="bi bi-plus-circle-fill me-2"></i> Thêm môn học
      </button>
    </div>

    <!-- Main Content Card -->
    <div class="data-card shadow-sm border-0 mb-4 bg-white p-3" style="border-radius: 16px;">
      <!-- Table Controls -->
      <div class="table-controls mb-4">
        <div class="row g-3 align-items-center">
          <div class="col-lg-5 col-md-12">
            <div class="navbar-search m-0 position-relative">
              <i class="bi bi-search search-icon position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
              <input 
                type="text" 
                v-model="searchKeyword" 
                class="form-control flux-input ps-5"
                placeholder="Tìm kiếm môn học theo tên hoặc mã..." 
              />
            </div>
          </div>
          <div class="col-lg-7 col-md-12">
            <div class="d-flex flex-wrap justify-content-lg-end gap-2">
              <select v-model="filterCredits" class="form-select flux-input" style="width: auto;">
                <option value="">Tất cả tín chỉ</option>
                <option v-for="c in [1, 2, 3, 4, 5]" :key="c" :value="c">{{ c }} tín chỉ</option>
              </select>
              <button class="btn btn-light border px-3 fw-600" @click="exportToExcel">
                <i class="bi bi-file-earmark-excel-fill text-success me-1"></i> Xuất Excel
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Table Content -->
      <div class="table-responsive">
        <table class="flux-table text-nowrap w-100">
          <thead>
            <tr>
              <th width="60" class="ps-3">#</th>
              <th>Mã môn học</th>
              <th>Tên môn học</th>
              <th class="text-center">Số tín chỉ</th>
              <th>Mô tả</th>
              <th class="text-end pe-3" width="120">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-rose" role="status">
                  <span class="visually-hidden">Đang tải...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="paginatedList.length === 0">
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-inbox display-6 d-block mb-3 opacity-25"></i>
                Không tìm thấy môn học nào.
              </td>
            </tr>
            <tr v-else v-for="(item, index) in paginatedList" :key="item.id">
              <td class="ps-3 text-muted fw-600">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
              <td>
                <span class="badge-subject">{{ item.ma_mon_hoc }}</span>
              </td>
              <td>
                <div class="fw-700 text-main">{{ item.ten_mon_hoc }}</div>
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
                  <button class="btn btn-action-v2 shadow-sm" @click="openEditModal(item)" title="Chỉnh sửa">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                  <button class="btn btn-action-v2 delete shadow-sm" @click="confirmDelete(item.id)" title="Xóa">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div class="table-footer d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top mt-3">
        <span class="small text-muted mb-3 mb-md-0">
          Hiển thị <b>{{ paginatedList.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }}</b> 
          - <b>{{ Math.min(currentPage * itemsPerPage, filteredList.length) }}</b> 
          trong tổng số <b>{{ filteredList.length }}</b> môn học
        </span>
        <nav v-if="totalPages > 1">
          <ul class="pagination pagination-sm m-0 gap-1">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <a class="page-link border-0 rounded-circle" href="#" @click.prevent="currentPage--">
                <i class="bi bi-chevron-left"></i>
              </a>
            </li>
            <li 
              class="page-item" 
              v-for="p in displayedPages" 
              :key="p" 
              :class="{ active: currentPage === p }"
            >
              <a class="page-link border-0 rounded-circle shadow-sm" href="#" @click.prevent="currentPage = p">{{ p }}</a>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
              <a class="page-link border-0 rounded-circle" href="#" @click.prevent="currentPage++">
                <i class="bi bi-chevron-right"></i>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Stats Section (Moved Below) -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="stat-card p-3 shadow-sm border-0 bg-white border-dashed">
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="icon-box-sm bg-rose-v3">
              <i class="bi bi-journal-text"></i>
            </div>
            <span class="small fw-700 text-muted text-uppercase">Tổng môn học</span>
          </div>
          <div class="h3 m-0 fw-800 text-main">{{ totalItems }}</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card p-3 shadow-sm border-0 bg-white border-dashed">
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="icon-box-sm bg-blue-v3">
              <i class="bi bi-mortarboard-fill"></i>
            </div>
            <span class="small fw-700 text-muted text-uppercase">Tổng tín chỉ</span>
          </div>
          <div class="h3 m-0 fw-800 text-main">{{ totalCredits }}</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card p-3 shadow-sm border-0 bg-white border-dashed">
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="icon-box-sm bg-orange-v3">
              <i class="bi bi-check-circle-fill"></i>
            </div>
            <span class="small fw-700 text-muted text-uppercase">Trạng thái</span>
          </div>
          <div class="h3 m-0 fw-800 text-main" style="font-size: 1.25rem;">Đã kết nối</div>
        </div>
      </div>
    </div>

    <!-- Subject Modal (Create/Edit) -->
    <div class="modal fade shadow-lg" id="monHocModal" tabindex="-1" aria-hidden="true" ref="modalEle">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden">
          <div class="modal-header border-0 bg-rose-dark text-white p-4">
            <h5 class="modal-title fw-800">{{ isEdit ? 'Cập nhật môn học' : 'Thêm môn học mới' }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4 bg-light-v2">
            <form @submit.prevent="handleSubmit">
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 12px;">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-700 small text-uppercase opacity-75">Mã môn học</label>
                            <input type="text" class="form-control flux-input" v-model="formData.ma_mon_hoc" required placeholder="Ví dụ: CS101">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-700 small text-uppercase opacity-75">Tên môn học</label>
                            <input type="text" class="form-control flux-input" v-model="formData.ten_mon_hoc" required placeholder="Ví dụ: Cấu trúc dữ liệu và Giải thuật">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-700 small text-uppercase opacity-75">Số tín chỉ</label>
                            <input type="number" class="form-control flux-input" v-model="formData.so_tin_chi" required min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-700 small text-uppercase opacity-75">Mô tả chi tiết</label>
                            <textarea class="form-control flux-input" v-model="formData.mo_ta" rows="3" placeholder="Nhập mô tả môn học..."></textarea>
                        </div>
                    </div>
                </div>
              
                <div class="mt-4 pt-2 d-flex gap-3">
                    <button type="button" class="btn btn-white border px-4 flex-fill fw-600 rounded-pill" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-rose-dark px-4 flex-fill fw-700 shadow-sm rounded-pill" :disabled="saving">
                        <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                        {{ isEdit ? 'Lưu thay đổi' : 'Tạo mới ngay' }}
                    </button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" ref="deleteModalEle">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 text-center p-4" style="border-radius: 20px;">
          <div class="modal-body">
            <div class="text-danger mb-3">
              <i class="bi bi-exclamation-octagon-fill display-4"></i>
            </div>
            <h5 class="fw-800 mb-2">Bạn chắc chứ?</h5>
            <p class="text-muted small">Hành động này không thể hoàn tác. Môn học sẽ bị xóa vĩnh viễn khỏi hệ thống.</p>
            <div class="d-flex flex-column gap-2 mt-4">
              <button type="button" class="btn btn-danger fw-700 py-2 rounded-pill" @click="handleDelete">Xác nhận xóa</button>
              <button type="button" class="btn btn-light fw-600 py-2 rounded-pill" data-bs-dismiss="modal">Quay lại</button>
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
      list: [],
      loading: false,
      saving: false,
      searchKeyword: "",
      filterCredits: "",
      isEdit: false,
      currentPage: 1,
      itemsPerPage: 5,
      formData: {
        id: null,
        ma_mon_hoc: "",
        ten_mon_hoc: "",
        so_tin_chi: 3,
        mo_ta: ""
      },
      deleteId: null,
      modal: null,
      deleteModal: null
    };
  },
  computed: {
    totalItems() {
      return this.list.length;
    },
    totalCredits() {
        return this.list.reduce((sum, item) => sum + (parseInt(item.so_tin_chi) || 0), 0);
    },
    filteredList() {
      let result = this.list;
      
      // Lọc theo từ khóa (live search)
      if (this.searchKeyword.trim()) {
        const kw = this.searchKeyword.toLowerCase();
        result = result.filter(item => 
          item.ma_mon_hoc.toLowerCase().includes(kw) || 
          item.ten_mon_hoc.toLowerCase().includes(kw)
        );
      }
      
      // Lọc theo tín chỉ
      if (this.filterCredits) {
        result = result.filter(item => parseInt(item.so_tin_chi) === parseInt(this.filterCredits));
      }
      
      return result;
    },
    paginatedList() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      return this.filteredList.slice(start, start + this.itemsPerPage);
    },
    totalPages() {
      return Math.ceil(this.filteredList.length / this.itemsPerPage);
    },
    displayedPages() {
      const current = this.currentPage;
      const total = this.totalPages;
      if (total <= 3) return Array.from({ length: total }, (_, i) => i + 1);
      if (current === 1) return [1, 2, 3];
      if (current === total) return [total - 2, total - 1, total];
      return [current - 1, current, current + 1];
    }
  },
  watch: {
    // Reset về trang 1 khi lọc hoặc tìm kiếm
    searchKeyword() { this.currentPage = 1; },
    filterCredits() { this.currentPage = 1; }
  },
  mounted() {
    this.getData();
    if (window.bootstrap) {
      this.modal = new window.bootstrap.Modal(this.$refs.modalEle);
      this.deleteModal = new window.bootstrap.Modal(this.$refs.deleteModalEle);
    }
  },
  methods: {
    async getData() {
      this.loading = true;
      try {
        const res = await baseRequestAdmin.get("mon-hocs/get-data");
        const data = res.data.list || res.data.data || res.data || [];
        this.list = Array.isArray(data) ? data : [];
      } catch (err) {
        console.error("Lỗi lấy data môn học:", err);
      } finally {
        this.loading = false;
      }
    },
    openCreateModal() {
      this.isEdit = false;
      this.formData = { id: null, ma_mon_hoc: "", ten_mon_hoc: "", so_tin_chi: 3, mo_ta: "" };
      this.modal.show();
    },
    openEditModal(item) {
      this.isEdit = true;
      this.formData = { ...item };
      this.modal.show();
    },
    async handleSubmit() {
      this.saving = true;
      try {
        if (this.isEdit) {
          await baseRequestAdmin.put(`mon-hocs/update/${this.formData.id}`, this.formData);
        } else {
          await baseRequestAdmin.post("mon-hocs/create", this.formData);
        }
        this.modal.hide();
        this.getData();
      } catch (err) {
        alert("Lỗi hệ công khi lưu môn học!");
      } finally {
        this.saving = false;
      }
    },
    confirmDelete(id) {
        this.deleteId = id;
        this.deleteModal.show();
    },
    async handleDelete() {
      try {
        await baseRequestAdmin.delete(`mon-hocs/delete/${this.deleteId}`);
        this.deleteModal.hide();
        this.getData();
      } catch (err) {
        alert("Lỗi khi xóa!");
      }
    },
    exportToExcel() {
      const data = this.filteredList;
      if (data.length === 0) return alert("Không có dữ liệu để xuất!");

      let content = "STT\tMã Môn Học\tTên Môn Học\tSố Tín Chỉ\tMô Tả\n";
      data.forEach((item, index) => {
        content += `${index + 1}\t${item.ma_mon_hoc}\t${item.ten_mon_hoc}\t${item.so_tin_chi}\t${item.mo_ta || ""}\n`;
      });

      const blob = new Blob(["\ufeff" + content], { type: "text/csv;charset=utf-8" });
      const url = URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", `Danh_sach_mon_hoc_${new Date().getTime()}.xls`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  }
};
</script>

<style scoped>
.text-main { color: #003366; }
.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }
.fw-600 { font-weight: 600; }
.bg-light-v2 { background: #f8fafc; }

.btn-new {
  background: #003366;
  color: #fff;
  border: none;
  padding: 12px 28px;
  border-radius: 12px;
  font-weight: 700;
  transition: all 0.3s;
}
.btn-new:hover { background: #002244; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,51,102,0.2); }

.badge-subject {
  background: rgba(190, 18, 60, 0.08);
  color: #BE123C;
  padding: 6px 14px;
  border-radius: 8px;
  font-family: 'JetBrains Mono', monospace;
  font-weight: 700;
  font-size: 13px;
  display: inline-block;
}

.flux-input {
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  padding: 10px 16px;
  background: #fdfdfd;
}
.flux-input:focus {
  border-color: #003366;
  box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.1);
  background: #fff;
}

.flux-table thead th {
  background: #f1f5f9;
  color: #475569;
  text-transform: uppercase;
  font-size: 11px;
  letter-spacing: 0.05em;
  padding: 16px 12px;
  border-bottom: 2px solid #e2e8f0;
}
.flux-table tbody td {
  padding: 18px 12px;
  vertical-align: middle;
  border-bottom: 1px solid #f1f5f9;
  font-size: 14px;
}
.flux-table tbody tr:hover { background: #f8fafc; }

.btn-action-v2 {
  width: 36px; height: 36px;
  background: #fff;
  color: #64748b;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  display: inline-flex; align-items: center; justify-content: center;
  transition: all 0.2s;
}
.btn-action-v2:hover { background: #003366; color: #fff; border-color: #003366; transform: translateY(-2px); }
.btn-action-v2.delete:hover { background: #ef4444; border-color: #ef4444; }

/* Stats */
.stat-card {
  background: #fff;
  border-radius: 16px;
  transition: all 0.3s ease;
  border: 1px solid rgba(0,0,0,0.02);
}
.stat-card.border-dashed {
    border: 1px dashed #e2e8f0;
}
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 16px rgba(0,0,0,0.04) !important; }

.icon-box-sm {
  width: 36px; height: 36px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem;
}
.bg-rose-v3 { background: #fff1f2; color: #e11d48; }
.bg-blue-v3 { background: #eff6ff; color: #1d4ed8; }
.bg-orange-v3 { background: #fff7ed; color: #ea580c; }

.bg-rose-dark { background: #9F1239; }

/* Pagination */
.pagination .page-link {
  width: 36px; height: 36px;
  display: flex; align-items: center; justify-content: center;
  color: #64748b;
  font-weight: 600;
  background: transparent;
}
.pagination .page-item.active .page-link {
  background: #003366 !important;
  color: #fff !important;
}
.pagination .page-item.disabled .page-link { opacity: 0.5; }
</style>
