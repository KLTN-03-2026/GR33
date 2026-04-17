<template>
  <div class="thong-ke-management">
    <!-- Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="page-title">Phê duyệt yêu cầu</h3>
        <p class="page-subtitle">Quản lý và phê duyệt các yêu cầu cấp NFT hoặc ghi nhận dữ liệu từ sinh viên.</p>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-light border" @click="lay_du_lieu" :disabled="dang_tai" title="Làm mới">
          <i v-if="dang_tai" class="spinner-border spinner-border-sm"></i>
          <i v-else class="bi bi-arrow-clockwise"></i>
        </button>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="data-card mb-4 shadow-sm border-0">
        <button class="btn tab-btn me-2" :class="{ active: tab_hien_tai === 1 }" @click="tab_hien_tai = 1">
          <i class="bi bi-award me-2"></i> Yêu cầu cấp NFT
        </button>
        <button class="btn tab-btn me-2" :class="{ active: tab_hien_tai === 2 }" @click="tab_hien_tai = 2">
          <i class="bi bi-file-earmark-check me-2"></i> Duyệt dữ liệu mới
        </button>
        <button class="btn tab-btn" :class="{ active: tab_hien_tai === 3 }" @click="tab_hien_tai = 3">
          <i class="bi bi-lightning-charge-fill me-2"></i> Chờ đúc NFT
        </button>
      </div>

    <!-- Main Content -->
    <div class="data-card shadow-sm border-0">
      <div class="table-responsive">
        <table class="flux-table text-nowrap">
          <thead>
            <tr>
              <th width="60" class="text-center">STT</th>
                <th>Sinh viên</th>
              <th>Loại hồ sơ</th>
              <th>Thông tin hồ sơ</th>
              <th class="text-center">Ngày yêu cầu</th>
              <th class="text-end pe-4" width="200">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="dang_tai">
              <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-rose" role="status"></div>
                <div class="mt-2 text-muted">Đang tải dữ liệu...</div>
              </td>
            </tr>
            <tr v-else-if="danh_sach.length === 0">
              <td colspan="6" class="text-center py-5">
                <div class="empty-state">
                  <i class="bi bi-inbox d-block mb-3 opacity-25" style="font-size: 3rem"></i>
                  <span class="text-muted">Không có yêu cầu nào đang chờ phê duyệt.</span>
                </div>
              </td>
            </tr>
            <tr v-else v-for="(item, index) in danh_sach" :key="item.id + '_' + item.loai_ho_so">
              <td class="text-center fw-700 text-muted">#{{ index + 1 }}</td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar-sm me-3 bg-rose-subtle text-rose-dark fw-bold">
                    {{ lay_ky_tu_dau(item.sinh_vien?.ho_ten) }}
                  </div>
                  <div>
                    <div class="fw-700 text-dark">{{ item.sinh_vien?.ho_ten }}</div>
                    <div class="small text-muted">{{ item.sinh_vien?.ma_sinh_vien }}</div>
                  </div>
                </div>
              </td>
              <td class="small fw-700">
                <span :class="lay_lop_loai(item.loai_ho_so)">{{ item.loai_ho_so }}</span>
              </td>
              <td class="small text-dark fw-600">
                {{ item.ten_ho_so }}
                <button class="btn btn-link btn-sm p-0 ms-1 text-rose" @click="mo_modal_chi_tiet(item)">
                  <i class="bi bi-info-circle"></i>
                </button>
              </td>
              <td class="text-center small text-muted">{{ item.updated_at || '---' }}</td>
              <td class="text-end pe-4">
                <div v-if="tab_hien_tai === 3" class="d-flex justify-content-end gap-2">
                  <button v-if="item.nft_van_bang?.trang_thai === 1 || dang_duc_nft === item.id" class="btn btn-sm btn-secondary fw-700 px-3 rounded-pill" disabled>
                    <i class="spinner-border spinner-border-sm me-1"></i> Đang đúc...
                  </button>
                  <button v-else-if="$hasPermission(52)" class="btn btn-sm btn-dark fw-700 px-3 rounded-pill" @click="thuc_hien_duc_nft(item)">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Đúc NFT
                  </button>
                </div>
                <div v-else class="d-flex justify-content-end gap-2">
                  <button v-if="(tab_hien_tai === 1 && $hasPermission(52)) || (tab_hien_tai === 2 && $hasPermission(54))" class="btn btn-sm btn-outline-success fw-700 px-3 rounded-pill" @click="xu_ly_phe_duyet(item)">
                    <i class="bi bi-check-circle me-1"></i> Duyệt
                  </button>
                  <button v-if="(tab_hien_tai === 1 && $hasPermission(52)) || (tab_hien_tai === 2 && $hasPermission(54))" class="btn btn-sm btn-outline-danger fw-700 px-3 rounded-pill" @click="mo_modal_tu_choi(item)">
                    <i class="bi bi-x-circle me-1"></i> Từ chối
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Chi tiết -->
    <div class="modal fade" id="modalChiTiet" tabindex="-1" aria-hidden="true" ref="modal_chi_tiet_ele">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
          <div class="modal-header border-0 bg-rose-v2 text-white p-4" style="border-radius: 20px 20px 0 0;">
            <h5 class="modal-title fw-800">Thông tin chi tiết hồ sơ</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4" v-if="chi_tiet">
            <div class="row g-4">
              <!-- Cột trái: Thông tin chung & Minh chứng -->
              <div class="col-md-5 border-end">
                <div class="text-center mb-4">
                   <div class="avatar-lg mx-auto bg-rose-subtle text-rose-dark rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                      {{ lay_ky_tu_dau(chi_tiet.sinh_vien?.ho_ten) }}
                   </div>
                   <h5 class="fw-800 mb-1">{{ chi_tiet.sinh_vien?.ho_ten }}</h5>
                   <p class="text-muted small mb-0">MSSV: {{ chi_tiet.sinh_vien?.ma_sinh_vien }}</p>
                </div>

                <!-- Hiển thị Minh chứng File nếu có -->
                <div v-if="chi_tiet.file_dinh_kem" class="mb-4">
                    <label class="small text-muted text-uppercase fw-700 d-block mb-2">Minh chứng đính kèm</label>
                    <div class="evidence-preview border rounded-4 overflow-hidden bg-light text-center p-2" 
                         style="cursor: pointer" @click="window.open(chi_tiet.file_dinh_kem, '_blank')">
                        <img v-if="chi_tiet.file_dinh_kem.match(/\.(jpg|jpeg|png|gif)$/i)" 
                             :src="chi_tiet.file_dinh_kem" class="img-fluid rounded-3 shadow-sm" alt="Evidence">
                        <div v-else class="py-4">
                            <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
                            <div class="small fw-600 mt-2">Xem tệp đính kèm (PDF/Khác)</div>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-4">
                   <label class="small text-muted text-uppercase fw-700 d-block mb-1">Loại hồ sơ</label>
                   <div class="fw-700 text-rose">{{ chi_tiet.loai_ho_so }}</div>
                   <hr class="my-2 opacity-10">
                   <label class="small text-muted text-uppercase fw-700 d-block mb-1">Ngày yêu cầu</label>
                   <div class="small fw-600">{{ chi_tiet.updated_at || '---' }}</div>
                </div>
              </div>

              <!-- Cột phải: Chi tiết dữ liệu -->
              <div class="col-md-7">
                <h6 class="fw-800 text-uppercase small text-muted mb-3 border-bottom pb-2">Nội dung chi tiết dữ liệu</h6>
                
                <!-- Hiển thị theo loại: Bảng điểm -->
                <div v-if="chi_tiet.loai_ho_so === 'Bảng điểm'">
                   <div class="mb-3">
                     <label class="small text-muted fw-700">Môn học / Lớp học</label>
                     <div class="fw-800 mb-1 text-dark">{{ chi_tiet.lop_hoc?.mon_hoc?.ten_mon_hoc || 'N/A' }}</div>
                     <div class="small text-muted">Lớp: {{ chi_tiet.lop_hoc?.ten_lop }} ({{ chi_tiet.lop_hoc?.ma_lop }})</div>
                   </div>
                   <div class="row g-3">
                     <div class="col-6">
                        <div class="p-2 border rounded-3 bg-white">
                            <label class="small text-muted fw-700 d-block">Điểm quá trình</label>
                            <div class="h5 fw-800 text-primary mb-0">{{ chi_tiet.diem_qua_trinh }}</div>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="p-2 border rounded-3 bg-white">
                            <label class="small text-muted fw-700 d-block">Điểm cuối kỳ</label>
                            <div class="h5 fw-800 text-primary mb-0">{{ chi_tiet.diem_cuoi_ky }}</div>
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="p-3 bg-primary-subtle rounded-4 text-center border border-primary border-opacity-10">
                          <label class="small text-muted fw-700 d-block">Điểm tổng kết</label>
                          <div class="h3 fw-800 text-primary mb-0">{{ chi_tiet.diem_tong_ket }} ({{ chi_tiet.diem_chu }})</div>
                          <div class="small fw-700 text-primary opacity-75">Hệ 4: {{ chi_tiet.diem_he_4 }}</div>
                        </div>
                     </div>
                   </div>
                </div>

                <!-- Hiển thị theo loại: Dự án -->
                <div v-else-if="chi_tiet.loai_ho_so === 'Dự án'">
                   <div class="mb-3">
                     <label class="small text-muted fw-700 text-uppercase">Mã dự án</label>
                     <div class="fw-800 text-dark">{{ chi_tiet.ma_du_an || '---' }}</div>
                   </div>
                   <div class="mb-3">
                     <label class="small text-muted fw-700 text-uppercase">Tên dự án</label>
                     <div class="fw-800 fs-5 text-accent">{{ chi_tiet.ten_du_an }}</div>
                   </div>
                   <div class="mb-3">
                     <label class="small text-muted fw-700 text-uppercase">Mô tả dự án</label>
                     <div class="p-3 bg-light rounded-3 small text-dark" style="white-space: pre-line;">{{ chi_tiet.mo_ta || 'Không có mô tả.' }}</div>
                   </div>
                   <div v-if="chi_tiet.link_du_an" class="mb-3">
                     <label class="small text-muted fw-700 text-uppercase">Link liên kết</label>
                     <div class="p-2 border rounded-3 text-truncate">
                        <a :href="chi_tiet.link_du_an" target="_blank" class="small text-rose fw-700">
                            <i class="bi bi-box-arrow-up-right me-1"></i> {{ chi_tiet.link_du_an }}
                        </a>
                     </div>
                   </div>
                </div>

                <!-- Hiển thị theo loại: Chứng chỉ -->
                <div v-else-if="chi_tiet.loai_ho_so === 'Chứng chỉ'">
                   <div class="row g-3 mb-3">
                     <div class="col-6">
                        <label class="small text-muted fw-700 text-uppercase">Mã chứng chỉ</label>
                        <div class="fw-800 text-dark">{{ chi_tiet.ma_chung_chi || '---' }}</div>
                     </div>
                     <div class="col-6">
                        <label class="small text-muted fw-700 text-uppercase">Loại</label>
                        <div class="fw-800 text-dark">{{ chi_tiet.loai_chung_chi }}</div>
                     </div>
                   </div>
                   <div class="mb-3">
                     <label class="small text-muted fw-700 text-uppercase">Tên chứng chỉ</label>
                     <div class="fw-800 fs-5 text-accent">{{ chi_tiet.ten_chung_chi }}</div>
                   </div>
                   <div class="mb-3">
                     <label class="small text-muted fw-700 text-uppercase">Đơn vị cấp</label>
                     <div class="fw-700 text-dark p-2 bg-light rounded-3">
                        <i class="bi bi-building me-1"></i> {{ chi_tiet.don_vi_cap?.ten_don_vi || chi_tiet.ten_don_vi_cap_khac }}
                     </div>
                   </div>
                   <div class="row g-3">
                     <div class="col-6">
                        <label class="small text-muted fw-700">Điểm số / Kết quả</label>
                        <div class="h5 fw-800 text-success mb-0">{{ chi_tiet.diem_so || '---' }}</div>
                     </div>
                     <div class="col-6">
                        <label class="small text-muted fw-700">Xếp loại</label>
                        <div class="h5 fw-800 text-success mb-0">{{ chi_tiet.xep_loai || '---' }}</div>
                     </div>
                     <div class="col-6">
                        <div class="p-2 border rounded-3 bg-white">
                            <label class="small text-muted fw-700 d-block">Ngày cấp</label>
                            <div class="fw-700">{{ chi_tiet.ngay_cap }}</div>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="p-2 border rounded-3 bg-white">
                            <label class="small text-muted fw-700 d-block">Ngày hết hạn</label>
                            <div class="fw-700">{{ chi_tiet.ngay_het_han || 'Vĩnh viễn' }}</div>
                        </div>
                     </div>
                   </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 p-4 pt-0">
             <button class="btn btn-light border px-4 fw-600" data-bs-dismiss="modal">Đóng</button>
             <button v-if="(tab_hien_tai === 1 && $hasPermission(52)) || (tab_hien_tai === 2 && $hasPermission(54))" 
                class="btn btn-rose-v2 px-4 fw-700 shadow-sm" @click="xu_ly_phe_duyet(chi_tiet)" data-bs-dismiss="modal">Phê duyệt ngay</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Từ chối -->
    <div class="modal fade" id="modalTuChoi" tabindex="-1" aria-hidden="true" ref="modal_tu_choi_ele">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
          <div class="modal-header border-0 bg-danger text-white p-4" style="border-radius: 20px 20px 0 0;">
            <h5 class="modal-title fw-800">Từ chối yêu cầu</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="alert alert-warning small mb-3">
              Vui lòng nhập lý do từ chối để sinh viên có thể nắm được thông tin và điều chỉnh.
            </div>
            <div class="mb-3">
              <label class="form-label fw-700 small text-uppercase">Lý do từ chối</label>
              <textarea class="form-control flux-input" v-model="ly_do_tu_choi" rows="4" placeholder="VD: Sai thông tin điểm số, dự án chưa hoàn thiện..."></textarea>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-light border flex-fill fw-600" data-bs-dismiss="modal">Hủy bỏ</button>
              <button class="btn btn-danger flex-fill fw-700" @click="xac_nhan_tu_choi" :disabled="!ly_do_tu_choi || dang_luu">
                <i v-if="dang_luu" class="spinner-border spinner-border-sm me-1"></i> Xác nhận từ chối
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal Ký số Blockchain -->
    <div class="modal fade" id="modalKySo" tabindex="-1" aria-hidden="true" ref="modal_ky_so_ele">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
          <div class="modal-header border-0 bg-dark text-white p-4" style="border-radius: 20px 20px 0 0;">
            <div class="d-flex align-items-center">
                <i class="bi bi-patch-check-fill text-warning me-2 fs-4"></i>
                <h5 class="modal-title fw-800">Xác thực Ký số NFT</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4 text-center">
            <div v-if="item_dang_ky_so">
                <div class="p-3 bg-light rounded-4 mb-3 text-start">
                    <label class="small text-muted fw-700 text-uppercase">Hồ sơ xác thực</label>
                    <div class="fw-800 text-dark">{{ item_dang_ky_so.ten_ho_so }}</div>
                    <div class="small text-rose fw-700">{{ item_dang_ky_so.sinh_vien?.ho_ten }}</div>
                </div>

                <div class="wallet-status p-3 border rounded-4 mb-4" :class="lay_lop_trang_thai_vi()">
                    <div v-if="!dia_chi_dang_ket_noi">
                        <i class="bi bi-wallet2 fs-1 text-warning mb-2 d-block"></i>
                        <div class="fw-700">Chưa kết nối ví MetaMask</div>
                        <button class="btn btn-warning btn-sm mt-2 fw-700 px-4 rounded-pill" @click="ket_noi_vi">
                            <i class="bi bi-plug-fill me-1"></i> Kết nối ví ngay
                        </button>
                    </div>
                    <div v-else>
                        <i class="bi bi-patch-check-fill fs-1 text-success mb-2 d-block"></i>
                        <div class="fw-700 text-success">Ví đã kết nối thành công</div>
                        <div class="small text-muted text-truncate px-3">{{ dia_chi_dang_ket_noi }}</div>
                        
                        <div class="mt-2 small text-warning fw-600 px-2 border-top pt-2">
                            <i class="bi bi-info-circle me-1"></i> Vui lòng đảm bảo bạn đang dùng Ví Ủy Quyền của Giám đốc để ký số nhé.
                        </div>
                    </div>
                </div>

                <div class="hash-box p-2 bg-dark text-white-50 rounded-3 mb-4 small italic">
                    <div class="fw-bold text-white mb-1">Dữ liệu băm (Hash)</div>
                    <div class="text-truncate px-2">{{ item_dang_ky_so.hash }}</div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-light border flex-fill fw-600" data-bs-dismiss="modal">Để sau</button>
                    <button v-if="$hasPermission(52)" class="btn btn-dark flex-fill fw-800 shadow" 
                        @click="bat_dau_ky_so" 
                        :disabled="!dia_chi_dang_ket_noi || dang_ky_so">
                        <i v-if="dang_ky_so" class="spinner-border spinner-border-sm me-2"></i>
                        <i v-else class="bi bi-pen-fill me-2"></i> Ký số văn bằng
                    </button>
                </div>
                <div class="mt-3 small text-muted">
                    <i class="bi bi-info-circle me-1"></i> Chữ ký số sẽ được sử dụng để đúc NFT lên Blockchain.
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import baseRequestAdmin from "../../../core/baseRequestAdmin";
import web3Service from "../../../core/web3Service";

export default {
  name: "AdminPheDuyet",
  data() {
    return {
      tab_hien_tai: 1, 
      danh_sach: [],
      dang_tai: false,
      dang_luu: false,
      item_dang_xu_ly: null,
      chi_tiet: null,
      ly_do_tu_choi: "",
      modal_tu_choi: null,
      modal_chi_tiet: null,
      modal_ky_so: null,
      item_dang_ky_so: null,
      dia_chi_dang_ket_noi: "",
      vi_dang_ky_he_thong: "",
      dang_ky_so: false,
      dang_duc_nft: null,
    };
  },
  watch: {
    tab_hien_tai() {
      this.lay_du_lieu();
    },
  },
  mounted() {
    this.lay_du_lieu();
    if (window.bootstrap) {
      this.modal_tu_choi = new window.bootstrap.Modal(this.$refs.modal_tu_choi_ele);
      this.modal_chi_tiet = new window.bootstrap.Modal(this.$refs.modal_chi_tiet_ele);
      this.modal_ky_so = new window.bootstrap.Modal(this.$refs.modal_ky_so_ele);
    }
    // Lắng nghe sự kiện đổi ví từ MetaMask
    web3Service.lang_nghe_su_kien_vi(this.xu_ly_doi_tai_khoan);
  },
  beforeUnmount() {
    // Hủy lắng nghe khi component bị tiêu hủy
    web3Service.huy_lang_nghe(this.xu_ly_doi_tai_khoan);
  },
  methods: {
    lay_ky_tu_dau(ten) {
      if (!ten) return "??";
      const parts = ten.split(" ");
      return parts[parts.length - 1][0].toUpperCase();
    },
    lay_lop_loai(loai) {
      if (loai === "Bảng điểm") return "badge-code";
      if (loai === "Dự án") return "badge-subject text-warning bg-warning-subtle";
      return "badge-subject";
    },
    lay_du_lieu() {
      this.dang_tai = true;
      baseRequestAdmin.get("profile")
        .then(res => {
            if (res.data.status) {
                localStorage.setItem("nhan_vien_user", JSON.stringify(res.data.data));
            }
        });

      const endpoint = this.tab_hien_tai === 1 ? "phe-duyet/nft" : (this.tab_hien_tai === 2 ? "phe-duyet/du-lieu" : "phe-duyet/cho-duc-nft");
      baseRequestAdmin.get(endpoint)
        .then(res => {
          if (res.data.status) {
            this.danh_sach = res.data.data;
          }
        })
        .finally(() => {
          this.dang_tai = false;
        });
    },
    mo_modal_chi_tiet(item) {
      this.chi_tiet = item;
      this.modal_chi_tiet.show();
    },
    lay_vi_admin() {
        const userStr = localStorage.getItem("nhan_vien_user");
        if (userStr) {
            const user = JSON.parse(userStr);
            this.vi_dang_ky_he_thong = user?.vi_nhan_vien?.dia_chi_vi || user?.viNhanVien?.dia_chi_vi || "";
        }
    },
    xu_ly_phe_duyet(item) {
      if (this.tab_hien_tai === 2) {
        this.dang_tai = true;
        baseRequestAdmin.post("phe-duyet/xu-ly-du-lieu", {
          id: item.id,
          loai: item.loai_ho_so,
          hanh_dong: 1
        })
        .then(res => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            this.lay_du_lieu();
          }
        })
        .finally(() => {
          this.dang_tai = false;
        });
      } else {
        this.dang_tai = true;
        let typeVal = '';
        if (item.loai_ho_so === 'Bảng điểm') typeVal = 'bang_diem';
        else if (item.loai_ho_so === 'Chứng chỉ') typeVal = 'chung_chi';
        else if (item.loai_ho_so === 'Dự án') typeVal = 'du_an';

        baseRequestAdmin.post("nft/handle-request", {
            record_id: item.id,
            type: typeVal,
            action: 'approve'
        })
        .then(res => {
            if (res.data.status) {
                this.$toast.success(res.data.message);
                this.item_dang_ky_so = {
                    ...item,
                    nft_van_bang_id: res.data.nft_van_bang_id,
                    hash: res.data.hash
                };
                this.mo_modal_ky_so();
            } else {
                this.$toast.error(res.data.message);
            }
        })
        .finally(() => {
            this.dang_tai = false;
        });
      }
    },
    ket_noi_vi() {
        web3Service.yeu_cau_quyen_chon_tai_khoan()
          .then(tai_khoan => {
              this.dia_chi_dang_ket_noi = tai_khoan;
              this.$toast.success("Đã kết nối với ví: " + this.dia_chi_dang_ket_noi.substring(0, 10) + "...");
          })
          .catch(err => {
              console.error(err);
              if (err.code === 'ACTION_REJECTED' || err.code === 4001) {
                  this.$toast.warning("Bạn đã hủy yêu cầu kết nối ví trên MetaMask.");
              } else {
                  this.$toast.error("Lỗi kết nối ví: " + err.message);
              }
          });
    },
    xu_ly_doi_tai_khoan(accounts) {
        if (accounts.length > 0) {
            this.dia_chi_dang_ket_noi = accounts[0];
            this.$toast.info("Đã chuyển sang ví: " + this.dia_chi_dang_ket_noi.substring(0, 10) + "...");
        } else {
            this.dia_chi_dang_ket_noi = "";
            this.$toast.warning("Đã ngắt kết nối ví MetaMask.");
        }
    },
    mo_modal_ky_so() {
        this.lay_vi_admin(); 
        this.dia_chi_dang_ket_noi = ""; 
        this.dang_ky_so = false;
        this.modal_ky_so.show();
    },
    lay_lop_trang_thai_vi() {
        if (!this.dia_chi_dang_ket_noi) return 'border-warning bg-warning-subtle';
        return 'border-success bg-success-subtle shadow-sm';
    },
    bat_dau_ky_so() {
        if (!this.item_dang_ky_so || !this.dia_chi_dang_ket_noi) return;
        
        this.dang_ky_so = true;
        web3Service.ky_so(this.item_dang_ky_so.hash.trim())
          .then(chu_ky => {
              return baseRequestAdmin.post("nft/sign", {
                  nft_van_bang_id: this.item_dang_ky_so.nft_van_bang_id,
                  signature: chu_ky
              });
          })
          .then(res => {
              if (res.data.status) {
                  this.$toast.success("Phê duyệt và Ký số hồ sơ hoàn tất!");
                  this.modal_ky_so.hide();
                  this.lay_du_lieu();
              } else {
                  this.$toast.error(res.data.message);
              }
          })
          .catch(err => {
              console.error(err);
              if (err.code === 'ACTION_REJECTED' || err.code === 4001) {
                  this.$toast.warning("Bạn đã hủy yêu cầu ký số trên MetaMask.");
              } else {
                  this.$toast.error("Lỗi ký số hoặc ví: " + err.message);
              }
          })
          .finally(() => {
              this.dang_ky_so = false;
          });
    },
    mo_modal_tu_choi(item) {
      this.item_dang_xu_ly = item;
      this.ly_do_tu_choi = "";
      this.modal_tu_choi.show();
    },
    xac_nhan_tu_choi() {
      this.dang_luu = true;
      const endpoint = this.tab_hien_tai === 1 ? "phe-duyet/tu-choi-nft" : "phe-duyet/xu-ly-du-lieu";
      const payload = {
        id: this.item_dang_xu_ly.id,
        loai: this.item_dang_xu_ly.loai_ho_so,
        ly_do: this.ly_do_tu_choi,
        hanh_dong: 2 
      };

      baseRequestAdmin.post(endpoint, payload)
        .then(res => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            this.modal_tu_choi.hide();
            this.lay_du_lieu();
          }
        })
        .finally(() => {
          this.dang_luu = false;
        });
    },
    thuc_hien_duc_nft(item) {
        if (!item.nft_van_bang) {
             this.$toast.error("Không tìm thấy thông tin hợp lệ để đúc NFT.");
             return;
        }
        
        this.dang_duc_nft = item.id;
        baseRequestAdmin.post("nft/mint", {
            nft_van_bang_id: item.nft_van_bang.id
        })
          .then(res => {
              if (res.data.status) {
                  this.$toast.success(res.data.message);
                  this.lay_du_lieu();
              } else {
                  this.$toast.error(res.data.message);
              }
          })
          .catch(error => {
              this.$toast.error("Lỗi gửi yêu cầu đúc NFT: " + error.message);
          })
          .finally(() => {
              this.dang_duc_nft = null;
          });
    }
  }
};
</script>

<style scoped>
.tab-btn {
  padding: 10px 24px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  color: var(--text-muted);
  border: none;
  background: transparent;
  transition: all 0.2s;
}

.tab-btn:hover {
  background: var(--primary);
  color: var(--primary-darker);
}

.tab-btn.active {
  background: var(--primary-darker);
  color: white;
  box-shadow: 0 4px 12px rgba(190, 18, 60, 0.25);
}

.bg-rose-subtle { background: rgba(190, 18, 60, 0.08); }
.bg-warning-subtle { background: rgba(245, 158, 11, 0.1); }
.bg-primary-subtle { background: rgba(59, 130, 246, 0.1); }

/* Avatar LG */
.avatar-lg {
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
}

.evidence-preview {
    transition: all 0.3s;
}

.evidence-preview:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.bg-rose-v2 {
    background: linear-gradient(135deg, var(--primary-darker), #BE123C);
}
</style>
