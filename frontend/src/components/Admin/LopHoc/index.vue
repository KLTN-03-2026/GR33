<template>
  <div class="lop-hoc-management">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="page-title text-main fw-800">Quản lý lớp học (Class Ledger)</h3>
        <p class="page-subtitle text-muted">Quản lý danh sách lớp học, phân quyền giáo viên chủ nhiệm và giám sát sĩ số sinh viên trong hệ thống.</p>
      </div>
      <button class="btn-new shadow-sm" @click="openCreateModal">
        <i class="bi bi-plus-lg me-2"></i> Thêm lớp học
      </button>
    </div>

    <!-- Top Stats Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-4">
        <div class="stat-card p-4 shadow-sm border-0 h-100 position-relative overflow-hidden">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="icon-box-lg bg-rose-v3 text-rose">
              <i class="bi bi-people-fill"></i>
            </div>
            <span class="badge bg-light text-muted fw-600 rounded-pill px-2 border">+4 tháng này</span>
          </div>
          <div class="stat-label text-muted fw-700 small text-uppercase mb-1">TỔNG SỐ LỚP</div>
          <div class="display-4 fw-800 text-main">{{ totalClasses }}</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card p-4 shadow-sm border-0 h-100">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="icon-box-lg bg-blue-v3 text-primary">
              <i class="bi bi-person-badge-fill"></i>
            </div>
            <span class="badge bg-light text-success fw-600 rounded-pill px-2 border">Ổn định</span>
          </div>
          <div class="stat-label text-muted fw-700 small text-uppercase mb-1">TỔNG SỐ SINH VIÊN</div>
          <div class="display-4 fw-800 text-main">{{ totalStudents }}</div>
        </div>
      </div>
      <div class="col-md-4 text-white">
        <div class="stat-card p-4 shadow-sm border-0 h-100 bg-main-dark position-relative overflow-hidden luxury-accent">
          <div class="stat-label opacity-75 fw-700 small text-uppercase mb-2">CẬP NHẬT MỚI NHẤT</div>
          <div class="h5 fw-800 mb-2">{{ latestClassUpdate.name || '---' }}</div>
          <p class="small opacity-75 mb-3">{{ latestClassUpdate.time || 'Vừa xong' }}</p>
          <div class="d-flex align-items-center">
             <div class="avatar-group me-2">
                <div class="avatar-sm bg-light text-dark rounded-circle border border-2 border-primary">A</div>
                <div class="avatar-sm bg-warning text-dark rounded-circle border border-2 border-primary" style="margin-left: -10px;">B</div>
                <div class="avatar-sm bg-rose text-white rounded-circle border border-2 border-primary" style="margin-left: -10px;">+2</div>
             </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Card -->
    <div class="data-card shadow-sm border-0 mb-4 bg-white p-3" style="border-radius: 16px;">
      <!-- Table Controls -->
      <div class="table-controls mb-4 px-2">
        <div class="row g-3">
          <div class="col-lg-5 col-md-12">
            <div class="navbar-search m-0 position-relative">
              <i class="bi bi-filter search-icon position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
              <input 
                type="text" 
                v-model="searchKeyword" 
                class="form-control flux-input ps-5"
                placeholder="Lọc theo mã lớp hoặc giáo viên..." 
              />
            </div>
          </div>
          <div class="col-lg-7 col-md-12">
            <div class="d-flex flex-wrap justify-content-lg-end gap-2">
              <select v-model="filterYear" class="form-select flux-input" style="width: auto;">
                <option value="">Tất cả niên khóa</option>
                <option value="2023-2024">2023-2024</option>
                <option value="2024-2025">2024-2025</option>
                <option value="2025-2026">2025-2026</option>
              </select>
              <div class="d-flex gap-2">
                <button class="btn btn-light border px-3" @click="exportToExcel"><i class="bi bi-download"></i></button>
                <button class="btn btn-light border px-3"><i class="bi bi-printer"></i></button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Table Content -->
      <div class="table-responsive">
        <table class="lux-table w-100">
          <thead>
            <tr>
              <th width="60" class="ps-3">#</th>
              <th>MÃ LỚP</th>
              <th>TÊN LỚP</th>
              <th>GIÁO VIÊN CHỦ NHIỆM</th>
              <th class="text-center">SĨ SỐ</th>
              <th class="text-end pe-3">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
              </td>
            </tr>
            <tr v-else-if="paginatedList.length === 0">
              <td colspan="6" class="text-center py-5 text-muted">Không tìm thấy lớp học nào.</td>
            </tr>
            <tr v-else v-for="(item, index) in paginatedList" :key="item.id">
              <td class="ps-3 text-muted small fw-700">{{ String((currentPage - 1) * itemsPerPage + index + 1).padStart(2, '0') }}</td>
              <td>
                <span class="badge-code">{{ item.ma_lop_hoc }}</span>
              </td>
              <td>
                <div class="fw-800 text-main">{{ item.ten_lop_hoc }}</div>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar-initials me-2" :style="{ background: getRandomColor(item.giangVien?.ho_ten) }">
                    {{ getInitials(item.giangVien?.ho_ten) }}
                  </div>
                  <div class="fw-700 text-muted small text-truncate" style="max-width: 180px;">
                    {{ item.giangVien?.ho_ten || 'Chưa phân công' }}
                  </div>
                </div>
              </td>
              <td class="text-center">
                <span class="badge rounded-pill px-3 py-2" :class="getSiSoClass(item.bang_diems_count)">
                  {{ item.bang_diems_count || 0 }} / 40
                </span>
              </td>
              <td class="text-end pe-3">
                <div class="d-flex justify-content-end gap-2">
                  <button class="btn btn-link py-0 px-1 text-muted" @click="openEditModal(item)"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-link py-0 px-1 text-danger" @click="confirmDelete(item.id)"><i class="bi bi-trash"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-4 px-2">
        <span class="small text-muted">Hiển thị <b>{{ paginatedList.length }}</b> trong tổng số <b>{{ filteredList.length }}</b> lớp học</span>
        <nav v-if="totalPages > 1">
          <ul class="pagination pagination-sm lux-pagination gap-1">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <a class="page-link" href="#" @click.prevent="currentPage--"><i class="bi bi-chevron-left"></i></a>
            </li>
            <li class="page-item" v-for="p in displayedPages" :key="p" :class="{ active: currentPage === p }">
              <a class="page-link" href="#" @click.prevent="currentPage = p">{{ p }}</a>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
              <a class="page-link" href="#" @click.prevent="currentPage++"><i class="bi bi-chevron-right"></i></a>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Bottom Widgets -->
    <div class="row g-4 mt-2 mb-5">
      <!-- System Analysis -->
      <div class="col-lg-6 col-md-12">
        <div class="stat-card p-4 shadow-sm border-0 bg-white h-100">
          <div class="d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-bar-chart-fill text-main"></i>
            <h6 class="m-0 fw-800 text-main font-lexend">Phân tích hệ thống</h6>
          </div>
          <div class="mb-4">
            <div class="d-flex justify-content-between mb-2">
              <span class="small text-muted fw-700">Tỷ lệ lấp đầy trung bình</span>
              <span class="small fw-800 text-main">{{ fillRate }}%</span>
            </div>
            <div class="progress" style="height: 10px; border-radius: 5px;">
              <div class="progress-bar bg-main" :style="{ width: fillRate + '%' }" role="progressbar"></div>
            </div>
          </div>
          <div class="row text-center mt-3 g-2">
            <div class="col-6">
              <div class="bg-light-v2 p-3 rounded-card">
                <div class="small text-muted fw-700 text-uppercase mb-1">SỐ KHOA</div>
                <div class="h3 fw-800 text-main mb-0">08</div>
              </div>
            </div>
            <div class="col-6">
              <div class="bg-light-v2 p-3 rounded-card">
                <div class="small text-muted fw-700 text-uppercase mb-1">KHÓA HỌC</div>
                <div class="h3 fw-800 text-main mb-0">{{ totalMonHocs }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="col-lg-6 col-md-12">
        <div class="stat-card p-4 shadow-sm border-0 bg-white h-100">
          <div class="d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-clock-history text-main"></i>
            <h6 class="m-0 fw-800 text-main font-lexend">Hoạt động gần đây</h6>
          </div>
          <div class="activity-list">
             <div v-for="(act, i) in list.slice(0, 3)" :key="i" class="activity-item d-flex gap-3 mb-3 pb-3 border-bottom-dashed">
                <div class="activity-dot shadow-sm" :class="['bg-main', 'bg-info', 'bg-warning'][i % 3]"></div>
                <div class="flex-grow-1">
                   <div class="small fw-800 text-main">{{ i === 0 ? 'Cập nhật' : 'Khởi tạo' }} lớp {{ act.ma_lop_hoc }}</div>
                   <div class="text-muted" style="font-size: 11px;">{{ formatRelativeTime(act.updated_at) }} • Bởi Admin</div>
                </div>
             </div>
             <div v-if="list.length === 0" class="text-center py-4 text-muted small">Chưa có hoạt động nào.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <!-- Create/Edit Modal -->
    <div class="modal fade" id="lopHocModal" tabindex="-1" aria-hidden="true" ref="modalEle">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 card-glass" style="border-radius: 20px;">
          <div class="modal-header border-0 bg-main text-white p-4 luxury-accent">
            <h5 class="modal-title fw-800">{{ isEdit ? 'Cập nhật thông tin lớp' : 'Tạo mới lớp học' }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4 bg-light-v2">
            <form @submit.prevent="handleSubmit">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-800 small text-uppercase text-muted">Mã lớp học</label>
                        <input type="text" class="form-control flux-input" v-model="formData.ma_lop_hoc" required placeholder="Ví dụ: SE1901">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-800 small text-uppercase text-muted">Tên lớp học</label>
                        <input type="text" class="form-control flux-input" v-model="formData.ten_lop_hoc" required placeholder="Ví dụ: Kỹ thuật Phần mềm K19">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-800 small text-uppercase text-muted">Môn học</label>
                        <select class="form-select flux-input" v-model="formData.mon_hoc_id" required>
                             <option value="" disabled>Chọn môn học...</option>
                             <option v-for="m in monHocs" :key="m.id" :value="m.id">{{ m.ten_mon_hoc }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-800 small text-uppercase text-muted">Giảng viên chủ nhiệm</label>
                        <select class="form-select flux-input" v-model="formData.giang_vien_id" required>
                             <option value="" disabled>Chọn giảng viên...</option>
                             <option v-for="g in giangViens" :key="g.id" :value="g.id">{{ g.ho_ten }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-800 small text-uppercase text-muted">Năm học</label>
                        <input type="text" class="form-control flux-input" v-model="formData.nam_hoc" required placeholder="Ví dụ: 2025-2026">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-800 small text-uppercase text-muted">Học kỳ</label>
                        <select class="form-select flux-input" v-model="formData.hoc_ky" required>
                             <option value="1">Học kỳ 1</option>
                             <option value="2">Học kỳ 2</option>
                             <option value="3">Học kỳ hè</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-800 small text-uppercase text-muted">Trạng thái</label>
                        <select class="form-select flux-input" v-model="formData.trang_thai" required>
                             <option value="dang_mo">Đang mở</option>
                             <option value="da_ket_thuc">Đã kết thúc</option>
                        </select>
                    </div>
                </div>
                <div class="mt-5 d-flex gap-3">
                    <button type="button" class="btn btn-white border px-4 flex-fill fw-700 rounded-pill" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-main px-4 flex-fill fw-800 shadow rounded-pill" :disabled="saving">
                        {{ isEdit ? 'Lưu thay đổi' : 'Xác nhận tạo lớp' }}
                    </button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" ref="deleteModalEle">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 text-center p-4 shadow-lg" style="border-radius: 20px;">
          <div class="modal-body">
            <div class="text-danger mb-3"><i class="bi bi-x-circle-fill display-4"></i></div>
            <h5 class="fw-800 mb-2">Bạn chắc chứ?</h5>
            <p class="text-muted small">Cảnh báo: Dữ liệu lớp học và sinh viên liên quan sẽ bị ảnh hưởng.</p>
            <div class="d-flex flex-column gap-2 mt-4">
              <button class="btn btn-danger fw-800 py-2 rounded-pill shadow-sm" @click="handleDelete">Xác nhận xóa</button>
              <button class="btn btn-light fw-700 py-2 rounded-pill" data-bs-dismiss="modal">Hủy</button>
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
  name: "AdminLopHoc",
  data() {
    return {
      list: [],
      monHocs: [],
      giangViens: [],
      totalStudents: 0,
      loading: false,
      saving: false,
      searchKeyword: "",
      filterYear: "",
      currentPage: 1,
      itemsPerPage: 5,
      isEdit: false,
      formData: {
        id: null,
        ma_lop_hoc: "",
        ten_lop_hoc: "",
        mon_hoc_id: "",
        giang_vien_id: "",
        nam_hoc: "2025-2026",
        hoc_ky: 1,
        trang_thai: "dang_mo"
      },
      deleteId: null,
      modal: null,
      deleteModal: null
    };
  },
  computed: {
    totalClasses() { return this.list.length; },
    totalMonHocs() { return this.monHocs.length; },
    fillRate() {
        if (this.list.length === 0) return 0;
        const totalMax = this.list.length * 40;
        const currentTotal = this.list.reduce((sum, item) => sum + (item.bang_diems_count || 0), 0);
        return Math.round((currentTotal / totalMax) * 100) || 0;
    },
    filteredList() {
      let res = this.list;
      if (this.searchKeyword) {
        const kw = this.searchKeyword.toLowerCase();
        res = res.filter(i => 
          i.ma_lop_hoc.toLowerCase().includes(kw) || 
          i.ten_lop_hoc.toLowerCase().includes(kw) ||
          i.giangVien?.ho_ten?.toLowerCase().includes(kw)
        );
      }
      if (this.filterYear) {
        res = res.filter(i => i.nam_hoc === this.filterYear);
      }
      return res;
    },
    paginatedList() {
      const s = (this.currentPage - 1) * this.itemsPerPage;
      return this.filteredList.slice(s, s + this.itemsPerPage);
    },
    totalPages() {
      return Math.ceil(this.filteredList.length / this.itemsPerPage);
    },
    latestClassUpdate() {
        if (!this.list || this.list.length === 0) return { name: 'Chưa có dữ liệu', time: 'Vừa xong' };
        const latest = [...this.list].sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at))[0];
        let timeStr = 'Vừa xong';
        if (latest.updated_at) {
            const d = new Date(latest.updated_at);
            if (!isNaN(d.getTime())) {
                timeStr = "Cập nhật lúc " + d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            }
        }
        return {
            name: latest.ten_lop_hoc,
            ma: latest.ma_lop_hoc,
            time: timeStr
        };
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
    searchKeyword() { this.currentPage = 1; },
    filterYear() { this.currentPage = 1; }
  },
  mounted() {
    this.getData();
    this.getMonHocs();
    this.getGiangViens();
    this.getTotalStudents();
    if (window.bootstrap) {
      this.modal = new window.bootstrap.Modal(this.$refs.modalEle);
      this.deleteModal = new window.bootstrap.Modal(this.$refs.deleteModalEle);
    }
  },
  methods: {
    async getData() {
      this.loading = true;
      try {
        const res = await baseRequestAdmin.get("lop-hocs/get-data");
        this.list = res.data.data || [];
      } catch (err) { console.error(err); }
      finally { this.loading = false; }
    },
    async getMonHocs() {
      try {
        const res = await baseRequestAdmin.get("mon-hocs/get-data");
        this.monHocs = res.data.list || res.data.data || [];
      } catch (err) { console.error(err); }
    },
    async getGiangViens() {
      try {
        const res = await baseRequestAdmin.get("nhan-viens/get-data");
        const all = res.data.data || [];
        this.giangViens = all.filter(n => n.chuc_vu_id == 6);
      } catch (err) { console.error(err); }
    },
    async getTotalStudents() {
        try {
            const res = await baseRequestAdmin.get("sinh-viens/get-data");
            this.totalStudents = (res.data.data || []).length;
        } catch (err) { console.error(err); }
    },
    openCreateModal() {
      this.isEdit = false;
      this.formData = { ma_lop_hoc: "", ten_lop_hoc: "", mon_hoc_id: "", giang_vien_id: "", nam_hoc: "2025-2026", hoc_ky: 1, trang_thai: "dang_mo" };
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
        if (this.isEdit) await baseRequestAdmin.put(`lop-hocs/update/${this.formData.id}`, this.formData);
        else await baseRequestAdmin.post("lop-hocs/create", this.formData);
        this.modal.hide();
        this.getData();
      } catch (err) { alert("Lỗi khi lưu dữ liệu!"); }
      finally { this.saving = false; }
    },
    confirmDelete(id) {
      this.deleteId = id;
      this.deleteModal.show();
    },
    async handleDelete() {
      try {
        await baseRequestAdmin.delete(`lop-hocs/delete/${this.deleteId}`);
        this.deleteModal.hide();
        this.getData();
      } catch (err) { alert("Lỗi khi xóa!"); }
    },
    exportToExcel() {
      const data = this.filteredList;
      let csvContent = "STT,Mã lớp,Tên lớp,Giảng viên,Sĩ số,Năm học,Học kỳ\n";
      data.forEach((item, index) => {
          csvContent += `${index+1},${item.ma_lop_hoc},${item.ten_lop_hoc},${item.giangVien?.ho_ten || ''},${item.bang_diems_count}/40,${item.nam_hoc},${item.hoc_ky}\n`;
      });
      const blob = new Blob(["\ufeff" + csvContent], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.setAttribute("href", url);
      link.setAttribute("download", "Danh_sach_lop_hoc.xls");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },
    getInitials(name) {
      if (!name) return "??";
      return name.split(' ').map(n => n[0]).join('').slice(-2).toUpperCase();
    },
    getRandomColor(seed) {
      const colors = ["#6366f1", "#ec4899", "#f59e0b", "#10b981", "#3b82f6"];
      if (!seed) return colors[0];
      return colors[seed.length % colors.length];
    },
    getSiSoClass(count) {
        if (count >= 38) return "bg-danger-subtle text-danger border border-danger";
        if (count >= 30) return "bg-primary-subtle text-primary border border-primary";
        return "bg-light text-muted border";
    },
    formatRelativeTime(date) {
        if (!date) return "Vừa xong";
        const d = new Date(date);
        if (isNaN(d.getTime())) return "Vừa xong";
        const diff = Math.floor((new Date() - d) / 1000);
        if (diff < 60) return "Vừa xong";
        if (diff < 3600) return `${Math.floor(diff / 60)} phút trước`;
        if (diff < 86400) return `${Math.floor(diff / 3600)} giờ trước`;
        return d.toLocaleDateString();
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.lop-hoc-management {
  font-family: 'Plus Jakarta Sans', sans-serif;
  padding: 1rem;
}

.text-main { color: #003366; }
.bg-main { background: #003366; }
.bg-main-dark { background: #001f44; }
.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }
.fw-600 { font-weight: 600; }

.btn-new {
  background: #003366;
  color: #fff;
  border: none;
  padding: 10px 24px;
  border-radius: 12px;
  font-weight: 800;
  font-size: 14px;
  transition: all 0.3s;
}
.btn-new:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,51,102,0.25); background: #002244; }

/* Stat Cards */
.stat-card {
  border-radius: 20px;
  background: #fff;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.stat-card:hover { transform: translateY(-6px); box-shadow: 0 15px 35px rgba(0,0,0,0.08) !important; }

.icon-box-lg {
  width: 56px; height: 56px;
  border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px;
}
.bg-rose-v3 { background: rgba(190, 18, 60, 0.05); }
.bg-blue-v3 { background: rgba(37, 99, 235, 0.05); }

.luxury-accent::after {
  content: "";
  position: absolute;
  top: -50%; right: -20%;
  width: 200px; height: 200px;
  background: rgba(255,255,255,0.05);
  border-radius: 50%;
  pointer-events: none;
}

/* Avatar group */
.avatar-group { display: flex; }
.avatar-sm {
  width: 28px; height: 28px;
  display: flex; align-items: center; justify-content: center;
  font-size: 10px; font-weight: 800; cursor: default;
}

/* Table Controls */
.flux-input {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 10px 16px;
  font-size: 14px;
  font-weight: 600;
}
.flux-input:focus {
  background: #fff;
  box-shadow: 0 0 0 4px rgba(0,51,102,0.08);
  border-color: #003366;
}

/* Luxury Table */
.lux-table thead th {
  padding: 16px 12px;
  font-size: 11px;
  letter-spacing: 0.1em;
  color: #94a3b8;
  border-bottom: 2px solid #f1f5f9;
}
.lux-table tbody tr {
    transition: all 0.2s;
}
.lux-table tbody tr:hover {
    background: #f8fafc;
}
.lux-table tbody td {
  padding: 20px 12px;
  vertical-align: middle;
}

.badge-code {
  background: #f1f5f9;
  color: #003366;
  padding: 6px 12px;
  border-radius: 8px;
  font-weight: 800;
  font-size: 12px;
  font-family: 'JetBrains Mono', monospace;
}

.avatar-initials {
  width: 34px; height: 34px;
  border-radius: 10px;
  color: #fff;
  font-weight: 800;
  font-size: 11px;
  display: flex; align-items: center; justify-content: center;
}

/* Pagination */
.lux-pagination .page-link {
  border: 1px solid #e2e8f0;
  border-radius: 8px !important;
  color: #475569;
  font-weight: 700;
  padding: 6px 14px;
}
.lux-pagination .page-item.active .page-link {
  background: #003366; border-color: #003366; color: #fff;
  box-shadow: 0 4px 12px rgba(0,51,102,0.2);
}

/* Bottom widgets */
.bg-light-v2 { background: #f8fafc; }
.rounded-card { border-radius: 16px; }
.activity-dot {
  width: 10px; height: 10px;
  border-radius: 50%;
  margin-top: 5px; flex-shrink: 0;
}
.border-bottom-dashed { border-bottom: 1px dashed #e2e8f0; }

/* Font Lexend for titles */
.font-lexend { font-family: 'Plus Jakarta Sans', sans-serif; }

.card-glass {
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.95);
}
</style>
