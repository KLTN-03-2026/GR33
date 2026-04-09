<template>
  <div class="modal fade" id="bangDiemModal" tabindex="-1" aria-hidden="true" ref="modalEle">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden">
        <div class="modal-header border-0 bg-rose-v2 text-white p-4">
          <h5 class="modal-title fw-800">{{ isEdit ? 'Cập nhật Bảng Điểm' : 'Tạo Bảng Điểm mới' }}</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <form @submit.prevent="handleSubmit">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-700 small text-uppercase opacity-75">Tên sinh viên / Nhân viên</label>
                <input type="text" class="form-control flux-input" v-model="formData.ten_nguoi_dung" required placeholder="Nhập tên người được chấm điểm">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-700 small text-uppercase opacity-75">Khóa học / Dự án</label>
                <input type="text" class="form-control flux-input" v-model="formData.ten_khoa_hoc" required placeholder="Nhập tên khóa học / dự án">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-700 small text-uppercase opacity-75">Điểm số (0-10)</label>
                <input type="number" step="0.1" min="0" max="10" class="form-control flux-input" v-model="formData.diem_so" required placeholder="Ví dụ: 8.5">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-700 small text-uppercase opacity-75">Xếp loại</label>
                <select class="form-select flux-input" v-model="formData.xep_loai">
                  <option value="Xuất sắc">Xuất sắc</option>
                  <option value="Giỏi">Giỏi</option>
                  <option value="Khá">Khá</option>
                  <option value="Trung bình">Trung bình</option>
                  <option value="Yếu">Yếu</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label fw-700 small text-uppercase opacity-75">Nhận xét chi tiết</label>
                <textarea class="form-control flux-input" v-model="formData.nhan_xet" rows="3" placeholder="Nhập nhận xét của giảng viên/quản lý..."></textarea>
              </div>
            </div>
            
            <div class="mt-4 pt-2 d-flex gap-3">
              <button type="button" class="btn btn-light border px-4 flex-fill fw-600" data-bs-dismiss="modal">Hủy bỏ</button>
              <button type="submit" class="btn btn-rose-v2 px-4 flex-fill fw-700 shadow-sm" :disabled="saving">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                {{ isEdit ? 'Cập nhật' : 'Lưu bảng điểm' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import baseRequestAdmin from "../../../core/baseRequestAdmin";

export default {
  name: "FormBangDiemModal",
  data() {
    return {
      saving: false,
      isEdit: false,
      formData: {
        id: null,
        ten_nguoi_dung: "",
        ten_khoa_hoc: "",
        diem_so: 0,
        xep_loai: "Khá",
        nhan_xet: ""
      },
      modalInstance: null
    };
  },
  mounted() {
    if (window.bootstrap) {
      this.modalInstance = new window.bootstrap.Modal(this.$refs.modalEle);
    }
  },
  methods: {
    show(item = null) {
      if (item) {
        this.isEdit = true;
        this.formData = { ...item };
      } else {
        this.isEdit = false;
        this.formData = { id: null, ten_nguoi_dung: "", ten_khoa_hoc: "", diem_so: 0, xep_loai: "Khá", nhan_xet: "" };
      }
      this.modalInstance.show();
    },
    hide() {
      if (this.modalInstance) {
        this.modalInstance.hide();
      }
    },
    async handleSubmit() {
      this.saving = true;
      try {
        if (this.isEdit) {
          await baseRequestAdmin.put(`bang-diems/update/${this.formData.id}`, this.formData);
        } else {
          await baseRequestAdmin.post("bang-diems/create", this.formData);
        }
        this.hide();
        this.$emit('saved');
      } catch (err) {
        alert("Lỗi hệ thống khi lưu bảng điểm!");
      } finally {
        this.saving = false;
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
.fw-800 { font-weight: 800; }
</style>
