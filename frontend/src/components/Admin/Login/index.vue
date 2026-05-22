<template>
  <div class="auth-page px-3">
    <!-- Nền gradients tinh tế -->
    <div class="bg-blobs">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
    </div>

    <div class="auth-card">
      <div class="row g-0 h-100">
        <!-- Cột trái: Thông tin thương hiệu -->
        <div class="col-lg-6 d-none d-lg-flex auth-info-panel p-5 flex-column justify-content-between text-white">
          <div class="brand-header animate__animated animate__fadeInDown">
            <div class="brand-logo-md mb-3">
              <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h1 class="display-5 fw-800 mb-2">Hệ Thống<br />Quản Trị Nội Bộ</h1>
            <p class="opacity-75 lead">Truy cập vào trung tâm điều hành chuyên dụng cho cán bộ và nhân viên.</p>
          </div>

          <div class="info-footer animate__animated animate__fadeInUp">
            <div class="d-flex align-items-center gap-3 p-3 glass-box">
              <div class="icon-circle bg-white text-rose-dark">
                <i class="bi bi-lightning-charge-fill"></i>
              </div>
              <div class="small">
                <div class="fw-700">Hiệu suất tối ưu</div>
                <div class="opacity-75">Trải nghiệm quản trị hiện đại & mượt mà.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cột phải: Form đăng nhập -->
        <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-center bg-white relative">
          <div class="auth-form-container mx-auto w-100" style="max-width: 400px;">
            <div class="text-center mb-5 d-lg-none">
                <div class="brand-logo-sm mx-auto mb-3">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h2 class="fw-800">Admin Portal</h2>
            </div>
            
            <div class="mb-4">
              <h2 class="fw-800 text-main mb-2">Chào mừng trở lại</h2>
              <p class="text-muted small">Vui lòng nhập tài khoản quản trị để tiếp tục.</p>
            </div>

            <form @submit.prevent="xu_ly_dang_nhap" class="login-form">
              <div class="mb-3">
                <label class="form-label fw-700 small text-uppercase opacity-75">Tài khoản (Email)</label>
                <div class="input-group-custom">
                  <i class="bi bi-envelope icon-input"></i>
                  <input
                    type="email"
                    class="form-control flux-input-lg"
                    v-model="tai_khoan"
                    placeholder="admin@hocvien.edu"
                    :disabled="dang_tai"
                    autocomplete="username"
                  />
                </div>
              </div>

              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label fw-700 small text-uppercase opacity-75 m-0">Mật khẩu</label>
                    <router-link to="/admin/forgot-password" class="text-rose-dark small text-decoration-none fw-600">Quên mật khẩu?</router-link>
                </div>
                <div class="input-group-custom">
                  <i class="bi bi-key icon-input"></i>
                  <input
                    :type="hien_mat_khau ? 'text' : 'password'"
                    class="form-control flux-input-lg pe-5"
                    v-model="mat_khau"
                    placeholder="••••••••"
                    :disabled="dang_tai"
                    autocomplete="current-password"
                  />
                  <button type="button" class="btn-toggle-pass" @click="hien_mat_khau = !hien_mat_khau">
                    <i :class="hien_mat_khau ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                  </button>
                </div>
              </div>

              <div class="mb-4">
                <div class="form-check">
                  <input class="form-check-input custom-check" type="checkbox" v-model="ghi_nho_mat_khau" id="rememberMe">
                  <label class="form-check-label small fw-600 text-muted" for="rememberMe">
                    Ghi nhớ mật khẩu
                  </label>
                </div>
              </div>

              <button class="btn btn-rose-lg w-100 py-3 fw-800 shadow-rose" :disabled="dang_tai">
                <span v-if="dang_tai" class="spinner-border spinner-border-sm me-2"></span>
                {{ dang_tai ? 'Đang xác thực...' : 'Đăng nhập hệ thống' }}
              </button>
            </form>

            <div class="mt-5 pt-4 border-top text-center text-muted small">
                © 2026 Admin Portal. Bảo mật bởi <span class="fw-700 text-rose-dark">Học viện học thuật</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import baseRequestClient from "../../../core/baseRequestClient";
import { authStore } from "../../../core/authStore";

export default {
  name: "LoginPage",
  data() {
    return {
      tai_khoan: "",
      mat_khau: "",
      hien_mat_khau: false,
      ghi_nho_mat_khau: false,
      dang_tai: false
    };
  },
  created() {
    if (localStorage.getItem("nhan_vien_token")) {
      this.$router.replace("/admin");
    }
    const remembered_email = localStorage.getItem("remembered_admin_email");
    if (remembered_email) {
      this.tai_khoan = remembered_email;
      this.ghi_nho_mat_khau = true;
    }
  },
  methods: {
    xu_ly_dang_nhap() {
      this.dang_tai = true;

      const payload = {
        email: this.tai_khoan,
        mat_khau: this.mat_khau
    };

      baseRequestClient
        .post("login/nhan-vien", payload)
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            const token = res.data.token || res.data.data?.token || res.data.access_token;
            const thong_tin_user = res.data.data?.user || res.data.user || res.data.data || {};
            
            localStorage.setItem("nhan_vien_token", token);
            // Dùng store để cập nhật reactive + lưu localStorage luôn
            authStore.updateUser(thong_tin_user);

            if (this.ghi_nho_mat_khau) {
              localStorage.setItem("remembered_admin_email", this.tai_khoan);
            } else {
              localStorage.removeItem("remembered_admin_email");
            }

            this.$router.push("/admin");
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          if (err.response && err.response.data && err.response.data.errors) {
            const danh_sach_loi = err.response.data.errors;
            Object.values(danh_sach_loi).forEach((loi) => {
              this.$toast.error(loi[0]);
            });
          } else {
            const thong_bao_loi = err.response?.data?.message || "Đăng nhập không thành công. Xin thử lại.";
            this.$toast.error(thong_bao_loi);
          }
        })
        .finally(() => {
          this.dang_tai = false;
        });
    }
    }
    };
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f8fafc;
  position: relative;
  overflow: hidden;
  font-family: 'Inter', sans-serif;
}

/* Blobs Background decoration */
.bg-blobs {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  z-index: 1;
}
.blob {
  position: absolute;
  width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(190,18,60,0.08) 0%, rgba(190,18,60,0) 70%);
  border-radius: 50%;
  filter: blur(60px);
}
.blob-1 { top: -100px; left: -100px; }
.blob-2 { bottom: -100px; right: -100px; background: radial-gradient(circle, rgba(91,141,239,0.08) 0%, rgba(91,141,239,0) 70%); }

.auth-card {
  width: 100%;
  max-width: 1100px;
  background: white;
  border-radius: 28px;
  box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1);
  overflow: hidden;
  z-index: 2;
  border: 1px solid rgba(255,255,255,0.7);
}

.auth-info-panel {
  background: linear-gradient(135deg, #BE123C 0%, #881337 100%);
  position: relative;
  overflow: hidden;
}
.auth-info-panel::after {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: url("https://www.transparenttextures.com/patterns/cubes.png");
  opacity: 0.1;
}

.brand-logo-md {
  width: 60px; height: 60px;
  background: white;
  color: #BE123C;
  border-radius: 18px;
  display: flex; align-items: center; justify-content: center;
  font-size: 2rem;
  box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

.glass-box {
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 16px;
}
.icon-circle {
  width: 40px; height: 40px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.2rem;
}

.brand-logo-sm {
    width: 64px; height: 64px;
    background: #BE123C;
    color: white;
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem;
}

.input-group-custom {
  position: relative;
}
.icon-input {
  position: absolute;
  left: 18px; top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 1.1rem;
  z-index: 5;
}

.btn-toggle-pass {
  position: absolute;
  right: 18px; top: 50%;
  transform: translateY(-50%);
  background: none; border: none;
  color: #94a3b8;
  font-size: 1.15rem;
  cursor: pointer;
  padding: 4px;
  display: flex; align-items: center; justify-content: center;
  transition: color 0.2s;
  z-index: 5;
}
.btn-toggle-pass:hover { color: #BE123C; }

.flux-input-lg {
  padding: 14px 18px 14px 50px;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  font-size: 1rem;
  font-weight: 500;
  transition: all 0.3s;
  background: #f8fafc;
}
.flux-input-lg:focus {
  background: white;
  border-color: #BE123C;
  box-shadow: 0 0 0 4px rgba(190,18,60,0.1);
  outline: none;
}

.custom-check {
    width: 1.1em; height: 1.1em;
    cursor: pointer;
}
.custom-check:checked {
    background-color: #BE123C;
    border-color: #BE123C;
}

.btn-rose-lg {
  background: #BE123C;
  color: white;
  border-radius: 14px;
  transition: all 0.3s;
  border: none;
}
.btn-rose-lg:hover {
  background: #9f1239;
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(190,18,60,0.3);
  color: white;
}
.btn-rose-lg:active {
  transform: translateY(0);
}

.shadow-rose {
  box-shadow: 0 4px 6px -1px rgba(190,18,60,0.1), 0 2px 4px -1px rgba(190,18,60,0.06);
}

.fw-800 { font-weight: 800; }
.text-rose-dark { color: #BE123C; }

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.login-form {
  animation: fadeIn 0.6s ease-out;
}
@media (max-width: 1024px) {
  .auth-card { grid-template-columns: 1fr; }
  .auth-info-panel { display: none; }
  .auth-page { padding: 18px; }
}
</style>