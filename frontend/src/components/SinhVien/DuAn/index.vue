<template>
  <div class="du-an-management px-2">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="page-title text-dark fw-800">Dự án của tôi</h3>
        <p class="page-subtitle text-muted">Quản lý các sản phẩm và dự án thực tế của bạn.</p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <div v-if="$authSV.user.trang_thai != 1" class="text-end me-2">
          <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.8rem">
            <i class="bi bi-info-circle-fill me-1"></i>
            {{ $authSV.user.trang_thai == 2 ? 'Tạm dừng thêm dự án (Đang Bảo lưu)' : ($authSV.user.trang_thai == 3 ? 'Chức năng thêm dự án đã đóng (Tốt nghiệp)' : 'Tài khoản đang bị đình chỉ') }}
          </span>
        </div>
        <button class="btn-pink shadow-sm" @click="moModalThem" :disabled="$authSV.user.trang_thai != 1"
          :title="$authSV.user.trang_thai != 1 ? 'Bạn không thể thêm dự án ở trạng thái hiện tại' : 'Thêm dự án'">
          <i class="bi bi-plus-circle-fill me-2"></i> Thêm dự án mới
        </button>
      </div>
    </div>

    <!-- Main Content Card -->
    <div class="data-card shadow-sm border-0 bg-white" style="border-radius: 20px; overflow: hidden;">
      <div class="table-controls p-3 border-bottom d-flex justify-content-between align-items-center gap-3">
        <div class="navbar-search m-0" style="max-width: 400px; width: 100%;">
          <i class="bi bi-search search-icon"></i>
          <input type="text" v-model="tuKhoaTimKiem" placeholder="Tìm theo tên hoặc mã dự án..." />
        </div>
        <button class="btn btn-light-pink" @click="layDuLieu">
          <i class="bi bi-arrow-clockwise"></i>
        </button>
      </div>

      <div class="table-responsive">
        <table class="flux-table text-nowrap">
          <thead>
            <tr>
              <th width="60" class="text-center">STT</th>
              <th>Mã dự án</th>
              <th>Tên dự án</th>
              <th class="text-center">Duyệt</th>
              <th class="text-center">Trạng thái NFT</th>
              <th class="text-center">Yêu cầu NFT</th>
              <th class="text-end" width="160">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="dangTai">
              <td colspan="7" class="text-center py-5">
                <div class="spinner-border text-accent" role="status"></div>
              </td>
            </tr>
            <tr v-else-if="danhSachLoc.length === 0">
              <td colspan="7" class="text-center py-5">
                <div class="empty-state py-4">
                  <i class="bi bi-folder2-open d-block mb-2 opacity-25" style="font-size: 3rem"></i>
                  <span class="text-muted">Bạn chưa có dự án nào.</span>
                </div>
              </td>
            </tr>
            <tr v-else v-for="(item, index) in danhSachLoc" :key="item.id">
              <td class="text-center text-muted fw-600">#{{ index + 1 }}</td>
              <td class="fw-700 text-accent">{{ item.ma_du_an }}</td>
              <td>
                <div class="fw-700 text-main">{{ item.ten_du_an }}</div>
                <div class="small text-muted text-truncate" style="max-width: 300px;">{{ item.mo_ta || '---' }}</div>
              </td>
              <td class="text-center">
                <span class="badge shadow-sm" :class="layLopPheDuyet(item.is_phe_duyet)">
                  {{ layTenPheDuyet(item.is_phe_duyet) }}
                </span>
              </td>
              <td class="text-center">
                <span class="badge" :class="layLopTrangThai(item.trang_thai)">
                  {{ layTenTrangThai(item.trang_thai) }}
                </span>
                <div v-if="item.trang_thai === 1" class="mt-1 d-flex justify-content-center gap-2">
                  <a :href="'https://sepolia.etherscan.io/tx/' + item.nft_van_bang.tx_hash_thanh_cong" 
                     target="_blank" class="text-accent small text-decoration-none fw-700" title="Xem Etherscan">
                    <i class="bi bi-box-arrow-up-right"></i>
                  </a>
                  <a href="javascript:void(0)" @click="moModalQR(item.nft_van_bang)" 
                     class="text-dark small text-decoration-none fw-700" title="Xem mã QR">
                    <i class="bi bi-qr-code"></i>
                  </a>
                </div>
                <div v-else-if="item.trang_thai === 4" class="mt-1 text-center">
                  <a v-if="item.nft_van_bang?.tx_hash_burn" 
                     :href="'https://sepolia.etherscan.io/tx/' + item.nft_van_bang.tx_hash_burn" 
                     target="_blank" class="text-danger small text-decoration-none fw-700">
                    <i class="bi bi-fire me-1"></i>Bằng chứng Hủy
                  </a>
                  <span v-else class="text-muted small fw-600">
                    <i class="bi bi-slash-circle me-1"></i>Đã thu hồi (Legacy)
                  </span>
                </div>
              </td>
              <td class="text-center">
                <div class="d-flex justify-content-center align-items-center gap-2" style="min-height: 40px">
                  <button v-if="item.trang_thai == 0 && item.is_phe_duyet == 1" 
                    class="btn btn-sm btn-pink-outline rounded-pill fw-700 px-3"
                    @click="guiYeuCauNFT(item)"
                    :disabled="dangLuuNFT || $authSV.user.trang_thai == 0 || $authSV.user.trang_thai == 2"
                    :title="($authSV.user.trang_thai == 0 || $authSV.user.trang_thai == 2) ? 'Trạng thái tài khoản không cho phép đúc NFT' : 'Gửi yêu cầu'">
                    <i class="bi bi-send-check-fill me-1"></i> Gửi yêu cầu
                  </button>
                  <span v-else-if="item.trang_thai == 2" class="text-muted small fw-600">
                    <i class="bi bi-hourglass-split me-1"></i> Chờ duyệt
                   </span>
                  <span v-else-if="item.trang_thai == 1" class="text-success small fw-700">
                    <i class="bi bi-patch-check-fill me-1"></i> Đã cấp
                  </span>
                  <span v-else class="text-muted small italic">
                    Chờ phê duyệt hồ sơ
                  </span>
                </div>
              </td>
              <td class="text-end">
                <div class="d-flex justify-content-end gap-2">

                  <button class="btn btn-action-pink" @click="moModalChiTiet(item)" title="Xem chi tiết">
                    <i class="bi bi-eye-fill"></i>
                  </button>
                  <button class="btn btn-action-pink" @click="moModalSua(item)" :disabled="item.is_locked || $authSV.user.trang_thai == 0">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Thêm/Sửa -->
    <div class="modal fade" id="duAnModal" tabindex="-1" aria-hidden="true" ref="refsModal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden">
          <div class="modal-header border-0 bg-pink-gradient text-white p-4">
            <h5 class="modal-title fw-800">{{ laCapNhat ? 'Cập nhật Dự án' : 'Đăng ký Dự án mới' }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="xuLyLuu">
              <div class="mb-3" v-if="laCapNhat">
                <label class="form-label fw-700 small text-uppercase opacity-75">Mã dự án</label>
                <input type="text" class="form-control flux-input-lg bg-light" v-model="duLieuForm.ma_du_an" disabled placeholder="Sẽ được tạo tự động">
              </div>
              <div class="mb-3">
                <label class="form-label fw-700 small text-uppercase opacity-75">Tên dự án</label>
                <input type="text" class="form-control flux-input-lg" v-model="duLieuForm.ten_du_an" required placeholder="Nhập tên dự án">
              </div>
              <div class="mb-3">
                <label class="form-label fw-700 small text-uppercase opacity-75">Link sản phẩm (Github/Demo)</label>
                <input type="url" class="form-control flux-input-lg" v-model="duLieuForm.link_du_an" placeholder="https://github.com/...">
              </div>
              <div class="mb-4">
                <label class="form-label fw-700 small text-uppercase opacity-75">Mô tả dự án</label>
                <textarea class="form-control flux-input-lg" v-model="duLieuForm.mo_ta" rows="4" placeholder="Nhập mô tả chi tiết..."></textarea>
              </div>

              <div class="d-flex gap-3 mt-4">
                <button type="button" class="btn btn-light-pink flex-fill py-2 fw-600" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-pink flex-fill py-2 fw-800 shadow-pink" :disabled="dangLuu">
                  <span v-if="dangLuu" class="spinner-border spinner-border-sm me-2"></span>
                  {{ laCapNhat ? 'Cập nhật' : 'Gửi yêu cầu' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <ModalQR ref="modalQR" :item="selectedNft" />

    <!-- Modal Chi tiết -->
    <div class="modal fade" id="chiTietModal" tabindex="-1" aria-hidden="true" ref="refsModalChiTiet">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                <div class="modal-header border-0 bg-pink-gradient text-white p-4" style="border-radius: 24px 24px 0 0">
                    <h5 class="modal-title fw-800">Chi tiết dự án</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" v-if="itemDuocChon">
                    <div class="detail-card p-3 mb-3 bg-pink-light" style="border-radius: 16px;">
                        <label class="small text-uppercase fw-800 text-accent opacity-50">Tên dự án</label>
                        <div class="fw-800 fs-5 text-dark mt-1">{{ itemDuocChon.ten_du_an }}</div>
                        <div class="fw-600 text-accent small">Mã: {{ itemDuocChon.ma_du_an }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small text-uppercase fw-800 text-muted opacity-75">Mô tả</label>
                        <p class="text-dark mt-1">{{ itemDuocChon.mo_ta || 'Không có mô tả' }}</p>
                    </div>

                    <div class="mb-3" v-if="itemDuocChon.link_du_an">
                        <label class="small text-uppercase fw-800 text-muted opacity-75">Liên kết sản phẩm</label>
                        <div class="mt-1">
                            <a :href="itemDuocChon.link_du_an" target="_blank" class="text-accent fw-600 text-decoration-none">
                                <i class="bi bi-link-45deg"></i> Xem demo / Mã nguồn
                            </a>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <label class="small text-uppercase fw-800 text-muted opacity-75">Trạng thái hồ sơ</label>
                        <span class="badge" :class="layLopTrangThai(itemDuocChon.trang_thai)">
                            {{ layTenTrangThai(itemDuocChon.trang_thai) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Xác nhận NFT -->
    <div class="modal fade" id="confirmNftModal" tabindex="-1" aria-hidden="true" ref="refsModalNFT">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                <div class="modal-header border-0 bg-pink-gradient text-white p-4" style="border-radius: 24px 24px 0 0">
                    <h5 class="modal-title fw-800">Xác nhận yêu cầu NFT</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-4">
                        <i class="bi bi-shield-lock-fill text-accent display-1 opacity-25"></i>
                    </div>
                    <h5 class="fw-800 text-dark mb-3">Bạn chắc chắn muốn tiếp tục?</h5>
                    <p class="text-muted mb-0">Hồ sơ sau khi gửi yêu cầu sẽ được <span class="text-accent fw-700">KHÓA TUYỆT ĐỐI</span> để đảm bảo tính toàn vẹn của dữ liệu trong suốt quá trình đúc NFT trên Blockchain.</p>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light-pink flex-fill py-2 fw-600" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="button" class="btn btn-pink flex-fill py-2 fw-800 shadow-pink" 
                        @click="thucHienGuiYeuCau" :disabled="dangLuuNFT">
                        <span v-if="dangLuuNFT" class="spinner-border spinner-border-sm me-2"></span>
                        Xác nhận gửi
                    </button>
                </div>
            </div>
        </div>
    </div>


  </div>
</template>

<script>
import baseRequestSinhVien from "../../../core/baseRequestSinhVien";
import ModalQR from "../../Common/ModalQR.vue";
import { authStoreSinhVien } from "../../../core/authStoreSinhVien";

export default {
  name: "SinhVienDuAn",
  components: {
    ModalQR
  },
  data() {
    return {
      danhSach: [],
      dangTai: false,
      dangLuu: false,
      dangLuuNFT: false,
      laCapNhat: false,
      tuKhoaTimKiem: "",
      duLieuForm: {
        id: null,
        ma_du_an: "",
        ten_du_an: "",
        mo_ta: "",
        link_du_an: ""
      },
      itemDuocChon: null,
      itemYeuCau: null,
      selectedNft: null,
      instanceModal: null,
      instanceModalChiTiet: null,
      instanceModalNFT: null,
    };
  },
  computed: {
    danhSachLoc() {
      if (!this.tuKhoaTimKiem) return this.danhSach;
      const kw = this.tuKhoaTimKiem.toLowerCase();
      return this.danhSach.filter(item => 
        item.ten_du_an.toLowerCase().includes(kw) || 
        item.ma_du_an.toLowerCase().includes(kw)
      );
    }
  },
  mounted() {
    this.layDuLieu();
    if (window.bootstrap) {
      this.instanceModal = new window.bootstrap.Modal(this.$refs.refsModal);
      this.instanceModalChiTiet = new window.bootstrap.Modal(this.$refs.refsModalChiTiet);
      this.instanceModalNFT = new window.bootstrap.Modal(this.$refs.refsModalNFT);
    }
  },
  methods: {
    layTenTrangThai(trangThai) {
      if (trangThai == 1) return 'Đã Đúc NFT';
      if (trangThai == 2) return 'Chờ Duyệt';
      if (trangThai == 4) return 'Đã Thu Hồi';
      return 'Chưa Đúc';
    },
    moModalQR(nft) {
        this.selectedNft = nft;
        this.$refs.modalQR.show();
    },
    layTenPheDuyet(status) {
      if (status == 1) return 'Đã Duyệt';
      if (status == 2) return 'Từ Chối';
      return 'Chờ Duyệt';
    },
    layLopPheDuyet(status) {
      if (status == 1) return 'bg-success text-white px-3';
      if (status == 2) return 'bg-danger text-white px-3';
      return 'bg-warning text-dark px-3';
    },
    layLopTrangThai(trangThai) {
      if (trangThai == 1) return 'bg-success-subtle text-success border border-success';
      if (trangThai == 2) return 'bg-warning-subtle text-warning border border-warning';
      if (trangThai == 4) return 'bg-danger-subtle text-danger border border-danger fw-800';
      return 'bg-secondary-subtle text-secondary border border-secondary';
    },
    layDuLieu() {
      this.dangTai = true;
      // Đồng bộ profile để cập nhật localStorage
      baseRequestSinhVien.get("profile")
        .then(res => {
            if (res.data.status) {
                authStoreSinhVien.updateUser(res.data.data);
            }
        });

      baseRequestSinhVien.get("du-ans/get-data")
        .then(res => {
          this.danhSach = res.data.list || res.data.data || [];
        })
        .catch(err => {
          this.$toast.error("Không thể tải danh sách dự án!");
        })
        .finally(() => {
          this.dangTai = false;
        });
    },
    moModalThem() {
      this.laCapNhat = false;
      this.duLieuForm = { id: null, ma_du_an: "", ten_du_an: "", mo_ta: "", link_du_an: "" };
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
        ? baseRequestSinhVien.post(`du-ans/update/${this.duLieuForm.id}`, this.duLieuForm)
        : baseRequestSinhVien.post("du-ans/create", this.duLieuForm);

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
          const errors = err.response?.data?.errors;
          if (errors) {
            Object.values(errors).forEach(e => this.$toast.error(e[0]));
          } else {
            this.$toast.error("Đã xảy ra lỗi hệ thống!");
          }
        })
        .finally(() => {
          this.dangLuu = false;
        });
    },
    guiYeuCauNFT(item) {
        const userStr = localStorage.getItem("sinh_vien_user");
        const user = userStr ? JSON.parse(userStr) : null;
        
        // Kiểm tra linh hoạt cả 2 định dạng (snake_case và camelCase) của Laravel
        const vi = user?.vi_sinh_vien || user?.viSinhVien;

        if (!vi?.dia_chi_vi) {
            this.$toast.error("Tài khoản của bạn chưa được liên kết với ví Blockchain. Vui lòng cài đặt ví tại thông tin cá nhân trước khi thực hiện yêu cầu NFT.");
            return;
        }

        this.itemYeuCau = item;
        this.instanceModalNFT.show();
    },
    thucHienGuiYeuCau() {
        if (!this.itemYeuCau) return;
        
        this.dangLuuNFT = true;
        baseRequestSinhVien.post("nft/request", {
            record_id: this.itemYeuCau.id,
            type: 'du_an'
        }).then(res => {
            if (res.data.status) {
                this.$toast.success(res.data.message);
                this.instanceModalNFT.hide();
                this.layDuLieu();
            } else {
                this.$toast.error(res.data.message);
            }
        }).finally(() => {
            this.dangLuuNFT = false;
        });
    },

  }
};
</script>

<style scoped>
.page-title { margin: 0; }
.btn-pink {
  background: #db2777; color: white; border-radius: 14px;
  padding: 10px 20px; border: none; font-weight: 700; transition: all 0.3s;
}
.btn-pink:hover { background: #be185d; transform: translateY(-2px); }

.btn-light-pink {
  background: #fdf2f8; color: #db2777; border-radius: 12px;
  padding: 8px 16px; border: 1px solid #fce7f3; font-weight: 600;
}

.bg-pink-gradient { background: linear-gradient(135deg, #db2777 0%, #be185d 100%); }
.bg-pink-light { background: #fdf2f8; }

.btn-action-pink {
  width: 36px; height: 36px; border-radius: 10px;
  background: #fff; border: 1px solid #fce7f3; color: #db2777;
  display: flex; align-items: center; justify-content: center; transition: all 0.2s;
}
.btn-pink-outline {
  color: #db2777; border: 1.5px solid #db2777; transition: all 0.2s;
}
.btn-pink-outline:hover {
  background: #db2777; color: white;
}
.btn-action-pink:hover { background: #fdf2f8; transform: scale(1.1); }

.flux-input-lg {
  padding: 12px 16px; border-radius: 14px; border: 1px solid #e2e8f0;
  background: #fafafa; transition: all 0.3s;
}
.flux-input-lg:focus { border-color: #db2777; box-shadow: 0 0 0 4px rgba(219,39,119,0.1); outline: none; }

.shadow-pink { box-shadow: 0 10px 15px -3px rgba(219,39,119,0.25); }
.text-accent { color: #db2777; }
.fw-800 { font-weight: 800; }
</style>
