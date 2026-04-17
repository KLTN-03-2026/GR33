<template>
    <div class="admin-setting">
        <div class="page-header mb-4">
            <h3 class="page-title fw-800 text-dark">Thiết lập tài khoản Admin</h3>
            <p class="page-subtitle text-muted">Quản lý thông tin hồ sơ, bảo mật và kết nối Blockchain của bạn.</p>
        </div>

        <div class="row g-4">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3">
                <div class="setting-sidebar bg-white shadow-sm border-0 p-3" style="border-radius: 20px;">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                        <button class="nav-link active mb-2" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button">
                            <i class="bi bi-person-circle me-2"></i> Thông tin cá nhân
                        </button>
                        <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#tab-wallet" type="button">
                            <i class="bi bi-wallet2 me-2"></i> Ví nhân viên
                        </button>
                        <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#tab-password" type="button">
                            <i class="bi bi-shield-lock me-2"></i> Đổi mật khẩu
                        </button>
                        <hr class="my-3 opacity-10">
                        <button class="nav-link text-danger border-0 bg-transparent text-start" @click="dang_xuat_tat_ca" type="button">
                            <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất tất cả
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9">
                <div class="tab-content bg-white shadow-sm border-0 p-4" style="border-radius: 20px; min-height: 450px;">
                    <!-- 1. Profile Tab -->
                    <div class="tab-pane fade show active" id="tab-profile">
                        <h5 class="fw-800 mb-4">Hồ sơ cá nhân</h5>
                        <form @submit.prevent="cap_nhat_ho_so">
                            <div class="row g-4">
                                <!-- Avatar Upload -->
                                <div class="col-12 mb-2">
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="avatar-wrapper position-relative">
                                            <img :src="ho_so.hinh_anh || 'https://via.placeholder.com/150'" 
                                                class="avatar-preview-lg rounded-circle border shadow-sm" 
                                                alt="Avatar">
                                            <label for="avatarInput" class="avatar-edit-btn shadow-sm">
                                                <i class="bi bi-camera-fill"></i>
                                            </label>
                                            <input type="file" id="avatarInput" hidden accept="image/*" @change="chon_tep">
                                        </div>
                                        <div>
                                            <h6 class="fw-800 mb-1">Ảnh đại diện</h6>
                                            <p class="small text-muted mb-0">Hỗ trợ JPG, PNG. Tối đa 2MB.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Họ và tên</label>
                                    <input type="text" class="form-control flux-input" v-model="ho_so.ho_ten" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Email (Tài khoản)</label>
                                    <input type="email" class="form-control flux-input bg-light" v-model="ho_so.email" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Số điện thoại</label>
                                    <input type="text" class="form-control flux-input" v-model="ho_so.so_dien_thoai">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Chức vụ</label>
                                    <input type="text" class="form-control flux-input bg-light" :value="ho_so.chuc_vu?.ten_chuc_vu" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-700 small text-uppercase opacity-75">Địa chỉ</label>
                                    <textarea class="form-control flux-input" v-model="ho_so.dia_chi" rows="2"></textarea>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-rose px-4 py-2 fw-700 shadow-rose" :disabled="dang_tai.ho_so">
                                        <span v-if="dang_tai.ho_so" class="spinner-border spinner-border-sm me-2"></span>
                                        <i class="bi bi-save2 me-1"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 2. Wallet Tab -->
                    <div class="tab-pane fade" id="tab-wallet">
                        <h5 class="fw-800 mb-4">Kết nối Ví Blockchain</h5>
                        
                        <div class="alert alert-rose-subtle border-0 rounded-4 p-3 mb-4">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-exclamation-triangle-fill fs-4 text-rose me-3"></i>
                                <div>
                                    <h6 class="fw-800 text-rose-dark">CẢNH BÁO BẢO MẬT QUAN TRỌNG</h6>
                                    <p class="small mb-0 opacity-75 text-rose-dark">Địa chỉ ví Blockchain tuyệt đối **không được làm mất** để đảm bảo quyền quản trị trên Blockchain. Ví này được dùng để định danh nhân viên thực hiện các thao tác Ký số / Thu hồi NFT. Một khi đã thiết lập, hệ thống sẽ **KHÔNG cho phép thay đổi** địa chỉ ví này.</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="ho_so.vi_nhan_vien" class="p-4 border rounded-4 bg-light shadow-inner">
                            <label class="small fw-700 text-uppercase text-muted d-block mb-2">Địa chỉ Ví đã liên kết</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="wallet-address p-3 bg-white border flex-fill rounded-3 font-monospace fw-600 text-rose-dark overflow-hidden text-truncate">
                                    {{ ho_so.vi_nhan_vien.dia_chi_vi }}
                                </div>
                                <div class="badge bg-success-subtle text-success px-3 py-2 rounded-pill border border-success flex-shrink-0">
                                    <i class="bi bi-shield-check-fill me-1"></i> Đã bảo mật
                                </div>
                            </div>
                            <p class="small text-muted mt-3 mb-0 italic">Lưu ý: Mọi giao dịch ký số của bạn trên Sepolia sẽ gắn liền với địa chỉ ví này.</p>
                        </div>
                        
                        <div v-else>
                            <label class="form-label fw-800 text-rose">Nhập địa chỉ Ví Ethereum (Sepolia)</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-link-45deg"></i></span>
                                <input type="text" class="form-control flux-input-lg border-start-0" 
                                    v-model="form_vi.dia_chi_vi" placeholder="0x..." required>
                            </div>
                            <div class="form-check mb-4 small">
                                <input class="form-check-input" type="checkbox" id="checkWallet" v-model="xac_nhan_vi">
                                <label class="form-check-label text-muted" for="checkWallet">
                                    Tôi xác nhận địa chỉ ví trên là chính xác và tôi sẽ chịu trách nhiệm bảo mật cho ví này.
                                </label>
                            </div>
                            <button @click="cap_nhat_vi" class="btn btn-rose w-100 py-2 fw-800 shadow-rose" 
                                :disabled="!xac_nhan_vi || dang_tai.vi">
                                <span v-if="dang_tai.vi" class="spinner-border spinner-border-sm me-2"></span>
                                Cập nhật Ví nhân viên (Chỉ 1 lần duy nhất)
                            </button>
                        </div>
                    </div>

                    <!-- 3. Password Tab -->
                    <div class="tab-pane fade" id="tab-password">
                        <h5 class="fw-800 mb-4">Đổi mật khẩu bảo mật</h5>
                        <form @submit.prevent="doi_mat_khau" class="row g-3" style="max-width: 500px">
                            <div class="col-12">
                                <label class="form-label fw-700 small text-uppercase opacity-75">Mật khẩu cũ</label>
                                <input type="password" class="form-control flux-input" v-model="form_mat_khau.old_password" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-700 small text-uppercase opacity-75">Mật khẩu mới</label>
                                <input type="password" class="form-control flux-input" v-model="form_mat_khau.new_password" required placeholder="Tối thiểu 6 ký tự">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-700 small text-uppercase opacity-75">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control flux-input" v-model="form_mat_khau.new_password_confirmation" required>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-rose px-4 py-2 fw-700 shadow-rose" :disabled="dang_tai.mat_khau">
                                    <span v-if="dang_tai.mat_khau" class="spinner-border spinner-border-sm me-2"></span>
                                    Cập nhật mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import baseRequestAdmin from '../../../core/baseRequestAdmin';
import web3Service from '../../../core/web3Service';
import { authStore } from '../../../core/authStore';

export default {
    name: 'AdminSetting',
    data() {
        return {
            ho_so: {
                ho_ten: '',
                email: '',
                so_dien_thoai: '',
                dia_chi: '',
                hinh_anh: '',
                chuc_vu: null,
                vi_nhan_vien: null
            },
            tep_da_chon: null,
            form_vi: {
                dia_chi_vi: ''
            },
            xac_nhan_vi: false,
            form_mat_khau: {
                old_password: '',
                new_password: '',
                new_password_confirmation: ''
            },
            dang_tai: {
                ho_so: false,
                vi: false,
                mat_khau: false
            }
        };
    },
    mounted() {
        this.lay_thong_tin_ca_nhan();
    },
    methods: {
        lay_thong_tin_ca_nhan() {
            baseRequestAdmin.get('profile')
                .then(res => {
                    if (res.data.status) {
                        this.ho_so = res.data.data;
                    }
                });
        },
        chon_tep(event) {
            this.tep_da_chon = event.target.files[0];
            if (this.tep_da_chon) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.ho_so.hinh_anh = e.target.result;
                };
                reader.readAsDataURL(this.tep_da_chon);
            }
        },
        cap_nhat_ho_so() {
            this.dang_tai.ho_so = true;
            const formData = new FormData();
            formData.append('ho_ten', this.ho_so.ho_ten);
            formData.append('so_dien_thoai', this.ho_so.so_dien_thoai || '');
            formData.append('dia_chi', this.ho_so.dia_chi || '');
            if (this.tep_da_chon) {
                formData.append('hinh_anh', this.tep_da_chon);
            }

            baseRequestAdmin.post('update-profile', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
                .then(res => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.tep_da_chon = null;
                        // Cập nhật store trung tâm
                        authStore.updateUser(res.data.data);
                        this.lay_thong_tin_ca_nhan();
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch(err => {
                    const errors = err.response?.data?.errors;
                    if (errors) {
                        Object.values(errors).forEach(e => this.$toast.error(e[0]));
                    }
                })
                .finally(() => this.dang_tai.ho_so = false);
        },
        ket_noi_vi() {
            web3Service.lay_tai_khoan_ket_noi()
                .then(tai_khoan => {
                    if (tai_khoan) {
                        this.form_vi.dia_chi_vi = tai_khoan;
                        this.$toast.success("Đã lấy địa chỉ ví thành công!");
                    }
                })
                .catch(err => {
                    if (err.code === 4001 || err.code === 'ACTION_REJECTED') {
                        this.$toast.warning("Bạn đã hủy kết nối MetaMask.");
                    } else {
                        this.$toast.error(err.message || "Không thể kết nối MetaMask!");
                    }
                });
        },
        cap_nhat_vi() {
            if (!this.form_vi.dia_chi_vi) {
                this.$toast.error("Vui lòng nhập địa chỉ ví Ethereum của bạn!");
                return;
            }
            if (!this.xac_nhan_vi) {
                this.$toast.error("Vui lòng tích xác nhận thông tin!");
                return;
            }
            this.dang_tai.vi = true;

            web3Service.lay_tai_khoan_ket_noi()
                .then(tai_khoan => {
                    if (!tai_khoan) {
                        this.$toast.error("Không thể kết nối ví MetaMask!");
                        return Promise.reject("MISSING_ACCOUNT");
                    }
                    
                    if (this.form_vi.dia_chi_vi.toLowerCase() !== tai_khoan.toLowerCase()) {
                        this.$toast.error("Địa chỉ ví MetaMask đang kết nối không khớp với địa chỉ bạn đã nhập!");
                        return Promise.reject("MISMATCH_ACCOUNT");
                    }

                    return baseRequestAdmin.post('update-wallet', this.form_vi);
                })
                .then(res => {
                    if (res && res.data.status) {
                        this.$toast.success("Liên kết ví thành công và đã lưu vào hệ thống!");
                        this.lay_thong_tin_ca_nhan();
                    } else if (res) {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch(err => {
                    if (err === "MISSING_ACCOUNT" || err === "MISMATCH_ACCOUNT") return;
                    console.error("Lỗi liên kết ví:", err);
                    if (err.code === 4001 || err.info?.error?.code === 4001 || err.message?.includes('rejected')) {
                        this.$toast.warning("Bạn đã hủy yêu cầu liên kết ví!");
                    } else {
                        this.$toast.error("Lỗi kết nối ví: " + (err.message || "Không xác định"));
                    }
                })
                .finally(() => {
                    this.dang_tai.vi = false;
                });
        },
        doi_mat_khau() {
            this.dang_tai.mat_khau = true;
            baseRequestAdmin.post('change-password', this.form_mat_khau)
                .then(res => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        this.form_mat_khau = { old_password: '', new_password: '', new_password_confirmation: '' };
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch(err => {
                    const errors = err.response?.data?.errors;
                    if (errors) {
                        Object.values(errors).forEach(e => this.$toast.error(e[0]));
                    }
                })
                .finally(() => this.dang_tai.mat_khau = false);
        },
        dang_xuat_tat_ca() {
            baseRequestAdmin.post('logout-all')
                .then(res => {
                    if (res.data.status) {
                        this.$toast.success(res.data.message);
                        authStore.clearUser();
                        this.$router.push('/admin/login');
                    } else {
                        this.$toast.error(res.data.message);
                    }
                })
                .catch(err => {
                    console.error("Lỗi đăng xuất tất cả:", err);
                    authStore.clearUser();
                    this.$router.push('/admin/login');
                });
        }
    }
}
</script>

<style scoped>
.text-rose { color: #BE123C; }
.text-rose-dark { color: #881337; }
.alert-rose-subtle { background: #fff1f2; color: #881337; }
.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }
.shadow-rose { box-shadow: 0 10px 15px -3px rgba(190, 18, 60, 0.25); }

.setting-sidebar .nav-link {
    border-radius: 12px;
    padding: 12px 16px;
    color: #4b5563;
    font-weight: 600;
    transition: all 0.2s;
    border: 1px solid transparent;
    text-align: left;
}

.setting-sidebar .nav-link:hover {
    background: #fff1f2;
    color: #BE123C;
}

.setting-sidebar .nav-link.active {
    background: #BE123C !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(190, 18, 60, 0.3);
}

.flux-input {
    border-radius: 12px;
    padding: 10px 14px;
    border: 1px solid #e5e7eb;
    background: #fcfcfc;
    transition: all 0.2s;
}

.flux-input:focus {
    border-color: #BE123C;
    box-shadow: 0 0 0 4px rgba(190, 18, 60, 0.1);
    background: white;
}

.btn-rose {
    background: #BE123C;
    color: white;
    border: none;
    border-radius: 12px;
    transition: all 0.2s;
}

.btn-rose:hover:not(:disabled) {
    background: #9F1239;
    transform: translateY(-2px);
}

/* Avatar Styles */
.avatar-wrapper { width: 120px; height: 120px; }
.avatar-preview-lg { width: 100%; height: 100%; object-fit: cover; background: #f3f4f6; }
.avatar-edit-btn {
    position: absolute; bottom: 5px; right: 5px;
    width: 32px; height: 32px; background: white;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    cursor: pointer; border: 1px solid #e5e7eb; color: #BE123C; transition: all 0.2s;
}
.avatar-edit-btn:hover { background: #BE123C; color: white; transform: scale(1.1); }

.wallet-address { word-break: break-all; font-size: 14px; }
.shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); }
.bg-success-subtle { background: #d1fae5 !important; }
.text-success { color: #059669 !important; }
</style>
