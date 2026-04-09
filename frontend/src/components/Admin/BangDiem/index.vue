<template>
  <div class="bang-diem-management">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="page-title">Quản lý Bảng Điểm</h3>
        <p class="page-subtitle">Hệ thống quản lý điểm số, đánh giá và xếp loại.</p>
      </div>
      <button class="btn-new" @click="openCreateModal">
        <i class="bi bi-plus-circle-fill"></i> Nhanh nhập điểm
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
            v-model="searchKeyword" 
            placeholder="Tìm theo tên học viên, môn học..." 
            @keyup.enter="handleSearch"
          />
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-rose-v2 px-4" @click="handleSearch">
            Tìm kiếm
          </button>
          <button class="btn btn-light border" @click="getData">
            <i class="bi bi-arrow-clockwise"></i>
          </button>
        </div>
      </div>

      <!-- Table Content -->
      <div class="table-responsive">
        <table class="flux-table text-nowrap">
          <thead>
            <tr>
              <th width="80">#ID</th>
              <th>Họ và tên</th>
              <th>Khóa học / Dự án</th>
              <th class="text-center">Điểm số</th>
              <th class="text-center">Xếp loại</th>
              <th class="text-end" width="120">Hành động</th>
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
            <tr v-else-if="list.length === 0">
              <td colspan="6" class="text-center py-5">
                <div class="empty-state">
                  <i class="bi bi-clipboard2-data d-block mb-2 opacity-25" style="font-size: 3rem"></i>
                  <span class="text-muted">Chưa có dữ liệu điểm nào.</span>
                </div>
              </td>
            </tr>
            <tr v-else v-for="item in list" :key="item.id">
              <td class="fw-700 text-rose-dark">#{{ item.id }}</td>
              <td>
                <div class="fw-700 text-main">{{ item.ten_nguoi_dung }}</div>
                <div class="small text-muted text-truncate" style="max-width: 200px;" :title="item.nhan_xet">{{ item.nhan_xet || 'Không có nhận xét' }}</div>
              </td>
              <td class="small fw-600 text-muted">
                <i class="bi bi-book me-1"></i> {{ item.ten_khoa_hoc }}
              </td>
              <td class="text-center">
                <div class="score-badge" :class="getScoreClass(item.diem_so)">
                  {{ item.diem_so }}
                </div>
              </td>
              <td class="text-center">
                <span class="badge-xep-loai" :class="getLoaiClass(item.xep_loai)">{{ item.xep_loai }}</span>
              </td>
              <td class="text-end">
                <div class="d-flex justify-content-end gap-2">
                  <button class="btn btn-action shadow-sm" @click="openEditModal(item)">
                    <i class="bi bi-pencil-fill text-primary-darker"></i>
                  </button>
                  <button class="btn btn-action shadow-sm" @click="handleDelete(item.id)">
                    <i class="bi bi-trash-fill text-danger"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <FormBangDiemModal ref="modalRef" @saved="getData" />
  </div>
</template>

<script>
import baseRequestAdmin from "../../../core/baseRequestAdmin";
import FormBangDiemModal from "./FormBangDiemModal.vue";

export default {
  name: "AdminBangDiem",
  components: {
    FormBangDiemModal
  },
  data() {
    return {
      list: [],
      loading: false,
      searchKeyword: ""
    };
  },
  mounted() {
    this.getData();
  },
  methods: {
    getScoreClass(diem) {
      if(diem >= 8.5) return 'score-excellent';
      if(diem >= 7.0) return 'score-good';
      if(diem >= 5.0) return 'score-average';
      return 'score-poor';
    },
    getLoaiClass(loai) {
      if(loai === 'Xuất sắc') return 'bg-success text-white';
      if(loai === 'Giỏi') return 'bg-info text-white';
      if(loai === 'Khá') return 'bg-primary text-white';
      if(loai === 'Trung bình') return 'bg-warning text-dark';
      return 'bg-danger text-white';
    },
    async getData() {
      this.loading = true;
      try {
        const res = await baseRequestAdmin.get("bang-diems/get-data");
        this.list = res.data.list || res.data.data || res.data;
      } catch (err) {
        console.error("Lỗi lấy data bảng điểm:", err);
      } finally {
        this.loading = false;
      }
    },
    async handleSearch() {
      this.loading = true;
      try {
        const res = await baseRequestAdmin.get(`bang-diems/search?keyword=${this.searchKeyword}`);
        this.list = res.data.list || res.data.data || res.data;
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    openCreateModal() {
      this.$refs.modalRef.show();
    },
    async openEditModal(item) {
      this.$refs.modalRef.show(item);
    },
    async handleDelete(id) {
      if (!confirm("Xác nhận xóa bảng điểm này?")) return;
      try {
        await baseRequestAdmin.delete(`bang-diems/delete/${id}`);
        this.getData();
      } catch (err) {
        alert("Lỗi khi xóa!");
      }
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
  width: 34px; height: 34px;
  padding: 0;
  background: #fff;
  border: 1px solid var(--border-color);
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
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

.score-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  font-weight: 800;
  font-size: 14px;
}
.score-excellent { background: rgba(16, 185, 129, 0.15); color: #059669; }
.score-good { background: rgba(59, 130, 246, 0.15); color: #2563EB; }
.score-average { background: rgba(245, 158, 11, 0.15); color: #D97706; }
.score-poor { background: rgba(239, 68, 68, 0.15); color: #DC2626; }

.badge-xep-loai {
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 6px;
}
.text-rose-dark { color: var(--primary-darker); }
.fw-800 { font-weight: 800; }
</style>
