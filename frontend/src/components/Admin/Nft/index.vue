<template>
  <div class="nft-management">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="page-title fw-800 text-dark mb-0">Quản lý Blockchain & NFT</h3>
        <p class="text-muted small">Giám sát toàn bộ văn bằng đã được đúc và thực hiện truy vết danh tính người ký
          duyệt.</p>
      </div>
    </div>

    <!-- Main Content Card -->
    <div class="data-card shadow-sm border-0 bg-white overflow-hidden" style="border-radius: 20px;">
      <!-- Table Controls (Filters) -->
      <div class="table-controls p-3 border-bottom bg-light-subtle">
        <div class="row g-3 align-items-center">
          <div class="col-md-3">
            <label class="form-label small fw-800 text-uppercase opacity-50">Loại hồ sơ</label>
            <select class="form-select flux-input" v-model="loc.loai" @change="layDanhSach">
              <option value="TatCa">Tất cả loại</option>
              <option value="BangDiem">Bảng điểm</option>
              <option value="ChungChi">Chứng chỉ</option>
              <option value="DuAn">Dự án</option>
            </select>
          </div>
          <div class="col-md-7">
            <label class="form-label small fw-800 text-uppercase opacity-50">Trạng thái</label>
            <select class="form-select flux-input" v-model="loc.trang_thai" @change="layDanhSach">
              <option value="">Tất cả trạng thái</option>
              <option value="1">Thành công</option>
              <option value="3">Đang chờ đúc</option>
              <option value="2">Thất bại</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end justify-content-end" style="padding-top: 28px;">
            <button class="btn btn-light border" @click="layDanhSach" title="Làm mới">
              <i class="bi bi-arrow-clockwise"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Main NFT Table -->
      <div class="table-responsive">
        <table class="flux-table text-nowrap table-hover align-middle mb-0">
          <thead>
            <tr>
              <th width="100" class="ps-4">TOKEN</th>
              <th>SINH VIÊN</th>
              <th>LOẠI HỒ SƠ</th>
              <th>DATA HASH</th>
              <th class="text-center">TÌNH TRẠNG</th>
              <th class="text-center">CẬP NHẬT GẦN NHẤT</th>
              <th class="text-end pe-4" width="160">HÀNH ĐỘNG</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="dangTai">
              <td colspan="7" class="text-center py-5">
                <div class="spinner-border text-rose" role="status"></div>
              </td>
            </tr>
            <tr v-else-if="danhSach.length === 0">
              <td colspan="7" class="text-center py-5">
                <div class="empty-state">
                  <i class="bi bi-patch-exclamation d-block mb-2 opacity-25" style="font-size: 3rem"></i>
                  <span class="text-muted">Không tìm thấy dữ liệu NFT nào.</span>
                </div>
              </td>
            </tr>
            <tr v-else v-for="(item, index) in danhSach" :key="index">
              <td class="ps-4">
                <span v-if="item.token_id !== null" class="badge-token">#{{ item.token_id }}</span>
                <span v-else class="text-muted italic small">Pending...</span>
              </td>
              <td>
                <div class="fw-700 text-dark">{{ item.nftable?.sinh_vien?.ho_ten || 'N/A' }}</div>
                <div class="small text-muted">{{ item.nftable?.sinh_vien?.ma_sinh_vien || 'N/A' }}</div>
              </td>
              <td>
                <span :class="layLoaiClass(item.nftable_type)">{{ layTenLoai(item.nftable_type) }}</span>
              </td>
              <td class="small font-monospace text-muted opacity-75">
                {{ item.hash_du_lieu ? item.hash_du_lieu.substring(0, 12) + '...' : 'N/A' }}
              </td>
              <td class="text-center">
                <span :class="layTrangThaiClass(item.trang_thai)">{{ layTenTrangThai(item.trang_thai) }}</span>
              </td>
              <td class="text-center small text-muted">
                {{ formatTime(item.updated_at) }}
              </td>
              <td class="text-end pe-4">
                <div class="d-flex justify-content-end gap-2">
                  <button v-if="$hasPermission(51)" class="btn btn-action shadow-sm" title="Lịch sử giao dịch" @click="moModalHistory(item)">
                    <i class="bi bi-clock-history text-primary"></i>
                  </button>
                  <button v-if="$hasPermission(51)" class="btn btn-action shadow-sm" title="Truy vết người ký" @click="moModalTrace(item)">
                    <i class="bi bi-patch-check-fill text-rose"></i>
                  </button>
                  <p class="mb-0" v-if="item.trang_thai != 3"><a v-if="item.tx_hash_thanh_cong && $hasPermission(51)"
                      :href="'https://sepolia.etherscan.io/tx/' + item.tx_hash_thanh_cong" target="_blank"
                      class="btn btn-action shadow-sm" title="Xem trên Etherscan">
                      <i class="bi bi-box-arrow-up-right text-dark"></i>
                    </a></p>
                  <button v-if="item.trang_thai === 1" class="btn btn-action shadow-sm" title="Xem mã QR"
                    @click="moModalQR(item)">
                    <i class="bi bi-qr-code text-dark"></i>
                  </button>
                  <button v-if="item.trang_thai === 1 && $hasPermission(53)" class="btn btn-action shadow-sm" title="Thu hồi văn bằng"
                    @click="moModalThuHoi(item)">
                    <i class="bi bi-arrow-counterclockwise text-warning"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div class="table-footer d-flex flex-column flex-md-row justify-content-between align-items-center p-3 border-top"
        v-if="total > 0">
        <span class="small text-muted fw-600">Hiển thị {{ danhSach.length }} / {{ total }} bản ghi</span>
        <nav>
          <ul class="pagination pagination-sm m-0 gap-1">
            <li class="page-item" :class="{ disabled: page === 1 }">
              <a class="page-link border-0 rounded-circle" href="#" @click.prevent="page--; layDanhSach()">
                <i class="bi bi-chevron-left"></i>
              </a>
            </li>
            <li class="page-item" :class="{ disabled: !hasMore }">
              <a class="page-link border-0 rounded-circle" href="#" @click.prevent="page++; layDanhSach()">
                <i class="bi bi-chevron-right"></i>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Modals -->
    <ModalQR ref="modalQR" :item="selectedItem" />

    <!-- Traceability Modal -->
    <div class="modal fade" id="modalTrace" tabindex="-1" ref="modalTraceEle">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
          <div class="modal-header border-0 bg-rose text-white p-4">
            <h5 class="modal-title fw-800">Truy vết Văn bằng NFT</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div v-if="dangTaiTrace" class="text-center py-5">
              <div class="spinner-border text-rose" role="status"></div>
              <p class="mt-3 text-muted fw-600">Đang truy vết dữ liệu Blockchain...</p>
            </div>
            <div v-else-if="traceData" class="row g-4">
              <!-- Left: Signer Info -->
              <div class="col-md-5">
                <h6 class="text-rose fw-800 small text-uppercase mb-3">Người ký duyệt</h6>
                <div class="p-3 border rounded-4 text-center bg-light-subtle mb-3">
                  <div class="avatar-holder mx-auto mb-3">
                    <i class="bi bi-person-badge text-rose display-4"></i>
                  </div>
                  <h5 class="fw-800 text-dark mb-1">{{ traceData.signer_info.name }}</h5>
                  <div class="badge bg-rose-subtle text-rose px-3 rounded-pill mb-2">{{ traceData.signer_info.position
                  }}</div>
                  <p class="small text-muted mb-0">{{ traceData.signer_info.department }}</p>
                </div>
                <div class="mb-3">
                  <label class="small text-muted fw-700 text-uppercase d-block mb-1">Ví người ký</label>
                  <div class="text-truncate px-2 py-1 bg-dark text-white-50 rounded-2 small">{{
                    traceData.signer_info.wallet }}</div>
                </div>
              </div>

              <!-- Right: Content -->
              <div class="col-md-7">
                <h6 class="text-primary fw-800 small text-uppercase mb-3">Bằng chứng Blockchain</h6>
                <div class="p-3 border rounded-4 bg-white">
                  <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                    <span class="text-muted fw-600">Loại hồ sơ</span>
                    <span class="fw-800 text-dark">{{ layTenLoai(traceData.diploma_info.type) }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                    <span class="text-muted fw-600">Token ID</span>
                    <span class="badge-token">#{{ traceData.nft_info.token_id }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                    <span class="text-muted fw-600">Sinh viên</span>
                    <span class="fw-800 text-dark">{{ traceData.diploma_info.student }}</span>
                  </div>
                  <div v-if="traceData.integrity.is_tampered"
                    class="mt-3 alert alert-danger border-0 rounded-3 small py-2 shadow-sm">
                    <div class="d-flex align-items-center">
                      <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
                      <div>
                        <strong class="d-block">CẢNH BÁO: SAI LỆCH DỮ LIỆU!</strong>
                        Nội dung hồ sơ hiện tại không khớp với mã băm đã đúc.
                      </div>
                    </div>
                  </div>
                  <div v-else class="mt-3 alert alert-success border-0 rounded-3 small py-2 shadow-sm">
                    <i class="bi bi-shield-check me-1"></i> Dữ liệu khớp hoàn toàn với bản gốc.
                  </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                  <a :href="traceData.blockchain_proof.etherscan" target="_blank"
                    class="btn btn-dark flex-fill fw-700 rounded-pill">Etherscan</a>
                  <button class="btn btn-light border flex-fill fw-700 rounded-pill"
                    data-bs-dismiss="modal">Đóng</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- History Modal -->
    <div class="modal fade" id="modalHistory" tabindex="-1" ref="modalHistoryEle">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
          <div class="modal-header border-0 bg-rose text-white p-4">
            <h5 class="modal-title fw-800">Lịch sử giao dịch</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4" v-if="selectedItem">
            <div class="timeline">
              <div v-for="(log, lIdx) in selectedItem.lich_su_giao_dichs" :key="lIdx" class="d-flex gap-3 mb-4">
                <div class="flex-shrink-0">
                  <div class="rounded-pill bg-rose p-1" style="width: 10px; height: 10px;"></div>
                </div>
                <div>
                  <div class="fw-800 text-rose-dark small text-uppercase">{{ log.hanh_dong }}</div>
                  <div class="small text-muted">{{ formatTime(log.created_at) }}</div>
                  <div class="mt-1 p-2 bg-light rounded-3 small text-truncate fw-600 opacity-75"
                    style="max-width: 320px;">
                    TX: {{ log.transaction_hash }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Revoke Modal -->
    <div class="modal fade" id="modalRevoke" tabindex="-1" ref="modalRevokeEle">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
          <div class="modal-header border-0 bg-danger text-white p-4">
            <h5 class="modal-title fw-800">Xác nhận Thu hồi NFT</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4" v-if="selectedItem">
            <div class="alert alert-danger border-0 small mb-4 shadow-sm">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              Hành động này sẽ đánh dấu NFT là không hợp lệ và mở khóa hồ sơ gốc để chỉnh sửa.
            </div>

            <div class="mb-3">
              <label class="form-label fw-700 small opacity-50">LÝ DO THU HỒI</label>
              <textarea class="form-control flux-input" v-model="reasonRevoke" rows="4"
                placeholder="Nhập lý do chi tiết để thông báo cho sinh viên..."></textarea>
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-danger flex-fill fw-800 rounded-pill py-2 shadow-danger" @click="xuLyThuHoi"
                :disabled="dangTaiRevoke">
                <span v-if="dangTaiRevoke" class="spinner-border spinner-border-sm me-2"></span>
                XÁC NHẬN THU HỒI
              </button>
              <button class="btn btn-light border flex-fill fw-800 rounded-pill py-2"
                data-bs-dismiss="modal">HỦY</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import baseRequestAdmin from "../../../core/baseRequestAdmin";
import ModalQR from "../../Common/ModalQR.vue";

export default {
  name: "NftManagement",
  components: {
    ModalQR
  },
  data() {
    return {
      danhSach: [],
      loc: {
        loai: 'TatCa',
        trang_thai: ''
      },
      page: 1,
      total: 0,
      hasMore: false,
      dangTai: false,
      selectedItem: null,
      traceData: null,
      dangTaiTrace: false,
      modalTrace: null,
      modalHistory: null,
      modalRevoke: null,
      reasonRevoke: '',
      dangTaiRevoke: false
    };
  },
  mounted() {
    this.layDanhSach();
    if (window.bootstrap) {
      this.modalTrace = new window.bootstrap.Modal(this.$refs.modalTraceEle);
      this.modalHistory = new window.bootstrap.Modal(this.$refs.modalHistoryEle);
      this.modalRevoke = new window.bootstrap.Modal(this.$refs.modalRevokeEle);
    }
  },
  methods: {
    layDanhSach() {
      this.dangTai = true;
      baseRequestAdmin.get(`nft/get-data`, {
        params: {
          page: this.page,
          type: this.loc.loai,
          status: this.loc.trang_thai,
          limit: 10
        }
      })
        .then(res => {
          let responsePayload = res.data ? res.data : res;

          if (responsePayload.status || responsePayload.data) {
            const rawData = responsePayload.data;

            if (rawData && rawData.data && Array.isArray(rawData.data)) {
              this.danhSach = rawData.data;
              this.total = rawData.total;
              this.hasMore = rawData.next_page_url ? true : false;
            } else if (Array.isArray(rawData)) {
              this.danhSach = rawData;
              this.total = rawData.length;
              this.hasMore = false;
            } else {
              this.danhSach = [];
            }
          } else {
            this.$toast.error("Phản hồi từ Server không đúng cấu trúc chuẩn.");
          }
        })
        .catch(err => {
          console.error("Lỗi khi gọi API NFT:", err);
          this.$toast.error("Lỗi: " + (err.response?.data?.message || "Không thể kết nối đến máy chủ."));
        })
        .finally(() => {
          this.dangTai = false;
        });
    },
    layTenLoai(type) {
      if (!type) return 'Khác';
      if (type.includes('BangDiem')) return 'Bảng điểm';
      if (type.includes('ChungChi')) return 'Chứng chỉ';
      if (type.includes('DuAn')) return 'Dự án';
      return 'Khác';
    },
    layLoaiClass(type) {
      if (!type) return 'badge-type-gray';
      if (type.includes('BangDiem')) return 'badge-type-blue';
      if (type.includes('ChungChi')) return 'badge-type-purple';
      if (type.includes('DuAn')) return 'badge-type-orange';
      return 'badge-type-gray';
    },
    layTenTrangThai(st) {
      if (st === 0) return 'Chờ ký';
      if (st === 1) return 'Thành công (On-Chain)';
      if (st === 2) return 'Thất bại';
      if (st === 3) return 'Đang đúc...';
      if (st === 4) return 'Đã thu hồi';
      return 'Không xác định';
    },
    layTrangThaiClass(st) {
      if (st === 1) return 'badge-status-success';
      if (st === 3) return 'badge-status-warning animation-pulse';
      if (st === 2) return 'badge-status-danger';
      if (st === 4) return 'badge-status-neutral border-warning';
      return 'badge-status-neutral';
    },
    formatTime(time) {
      if (!time) return 'N/A';
      return new Date(time).toLocaleString('vi-VN');
    },
    moModalHistory(item) {
      this.selectedItem = item;
      this.modalHistory.show();
    },
    moModalQR(item) {
        this.selectedItem = item;
        this.$refs.modalQR.show();
    },
    moModalTrace(item) {
      if (!item.token_id && item.token_id !== 0) {
        this.$toast.warning("NFT chưa được cấp Token ID (đang xử lý).");
        return;
      }
      this.dangTaiTrace = true;
      this.traceData = null;
      this.modalTrace.show();

      baseRequestAdmin.get(`../nft/trace/${item.token_id}`)
        .then(res => {
            if (res.data.status) {
                this.traceData = res.data.data;
            } else {
                this.$toast.error(res.data.message || "Không thể lấy dữ liệu truy vết.");
                this.modalTrace.hide();
            }
        })
        .catch(err => {
          console.error(err);
          this.$toast.error("Lỗi khi gọi API truy vết.");
          this.modalTrace.hide();
        })
        .finally(() => {
          this.dangTaiTrace = false;
        });
    },
    moModalThuHoi(item) {
      this.selectedItem = item;
      this.reasonRevoke = '';
      this.modalRevoke.show();
    },
    xuLyThuHoi() {
      if (!this.reasonRevoke || this.reasonRevoke.length < 5) {
        this.$toast.warning("Vui lòng nhập lý do thu hồi (tối thiểu 5 ký tự).");
        return;
      }

      this.dangTaiRevoke = true;
      baseRequestAdmin.post(`nft/revoke`, {
        nft_van_bang_id: this.selectedItem.id,
        reason: this.reasonRevoke
      })
        .then(res => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            this.modalRevoke.hide();
            this.layDanhSach();
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch(err => {
          console.error(err);
          this.$toast.error("Lỗi khi gọi API thu hồi.");
        })
        .finally(() => {
          this.dangTaiRevoke = false;
        });
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.nft-management {
  font-family: 'Plus Jakarta Sans', sans-serif;
  padding: 10px;
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

.text-rose {
  color: #BE123C;
}

.text-rose-dark {
  color: #9f1239;
}

.bg-rose {
  background-color: #BE123C;
}

.bg-rose-subtle {
  background: #fff1f2;
}

.data-card {
  border-radius: 20px;
}

.flux-input {
  border-radius: 12px;
  border: 1.5px solid #f1f5f9;
  padding: 10px 15px;
  font-weight: 600;
  color: #1e293b;
  transition: all 0.2s;
}

.flux-input:focus {
  border-color: #BE123C;
  box-shadow: 0 0 0 4px rgba(190, 18, 60, 0.05);
}

/* Badges */
.badge-token {
  background: #fff1f2;
  color: #BE123C;
  padding: 2px 10px;
  border-radius: 8px;
  font-weight: 800;
  border: 1px solid #ffe4e6;
}

.badge-type-blue {
  background: #eff6ff;
  color: #1d4ed8;
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 0.75rem;
}

.badge-type-purple {
  background: #faf5ff;
  color: #7e22ce;
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 0.75rem;
}

.badge-type-orange {
  background: #fff7ed;
  color: #c2410c;
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 0.75rem;
}

.badge-type-gray {
  background: #f1f5f9;
  color: #64748b;
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 0.75rem;
}

.badge-status-success {
  background: #dcfce7;
  color: #15803d;
  padding: 4px 12px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.75rem;
}

.badge-status-warning {
  background: #fef9c3;
  color: #a16207;
  padding: 4px 12px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.75rem;
}

.badge-status-danger {
  background: #fee2e2;
  color: #b91c1c;
  padding: 4px 12px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.75rem;
}

.badge-status-neutral {
  background: #f1f5f9;
  color: #64748b;
  padding: 4px 12px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.75rem;
}

.btn-action {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #f1f5f9;
  background: white;
  transition: all 0.2s;
}

.btn-action:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.shadow-danger {
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
}

.bg-light-subtle {
  background-color: #f8fafc;
}

.animation-pulse {
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% {
    opacity: 0.7;
  }

  50% {
    opacity: 1;
  }

  100% {
    opacity: 0.7;
  }
}
</style>
