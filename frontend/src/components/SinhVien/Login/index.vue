<template>
  <div class="auth-page px-3 sinh-vien-login">
    <!-- Nền gradients tinh tế (Tone Blue cho sinh viên) -->
    <div class="bg-blobs">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
    </div>

    <div class="auth-card">
      <div class="row g-0 h-100">
        <!-- Cột trái: Thông tin dành cho sinh viên -->
        <div class="col-lg-6 d-none d-lg-flex auth-info-panel p-5 flex-column justify-content-between text-white">
          <div class="brand-header animate__animated animate__fadeInDown">
            <div class="brand-logo-md mb-3">
              <i class="bi bi-mortarboard-fill"></i>
            </div>
            <h1 class="display-5 fw-800 mb-2">Cổng Thông Tin<br />Sinh Viên</h1>
            <p class="opacity-75 lead">Truy cập kết quả học tập, chứng chỉ và hồ sơ năng lực cá nhân của bạn.</p>
          </div>

          <div class="info-footer animate__animated animate__fadeInUp">
            <div class="d-flex align-items-center gap-3 p-3 glass-box">
              <div class="icon-circle bg-white text-accent">
                <i class="bi bi-patch-check-fill"></i>
              </div>
              <div class="small">
                <div class="fw-700">Hồ sơ số an toàn</div>
                <div class="opacity-75">Tất cả chứng nhận được xác thực bởi Blockchain.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cột phải: Form đăng nhập -->
        <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-center bg-white relative">
          <div class="auth-form-container mx-auto w-100" style="max-width: 400px;">
            <div class="text-center mb-5 d-lg-none">
                <div class="brand-logo-sm mx-auto mb-3">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <h2 class="fw-800">Student Portal</h2>
            </div>
            
            <div class="mb-4">
              <h2 class="fw-800 text-accent mb-2">Đăng nhập Sinh viên</h2>
              <p class="text-accent opacity-75 small">Chào mừng bạn quay trở lại! Vui lòng nhập thông tin để vào hệ thống.</p>
            </div>

            <form @submit.prevent="xu_ly_dang_nhap" class="login-form">
              <div class="mb-3">
                <label class="form-label fw-700 small text-uppercase opacity-75">Email Sinh viên</label>
                <div class="input-group-custom">
                  <i class="bi bi-person-circle icon-input"></i>
                  <input
                    type="email"
                    class="form-control flux-input-lg"
                    v-model="email"
                    placeholder="2026@sinhvien.edu.vn"
                    :disabled="dang_tai"
                    required
                  />
                </div>
              </div>

              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label fw-700 small text-uppercase opacity-75 m-0">Mật khẩu</label>
                    <router-link to="/sinh-vien/forgot-password" class="text-accent small text-decoration-none fw-600">Quên mật khẩu?</router-link>
                </div>
                <div class="input-group-custom">
                  <i class="bi bi-shield-lock icon-input"></i>
                  <input
                    :type="hien_mat_khau ? 'text' : 'password'"
                    class="form-control flux-input-lg pe-5"
                    v-model="mat_khau"
                    placeholder="••••••••"
                    :disabled="dang_tai"
                    required
                  />
                  <button type="button" class="btn-toggle-pass" @click="hien_mat_khau = !hien_mat_khau">
                    <i :class="hien_mat_khau ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                  </button>
                </div>
              </div>

              <div class="mb-4">
                <div class="form-check">
                  <input class="form-check-input custom-check" type="checkbox" v-model="ghi_nho_mat_khau" id="rememberStudent">
                  <label class="form-check-label small fw-600 text-muted" for="rememberStudent">
                    Ghi nhớ mật khẩu
                  </label>
                </div>
              </div>

              <button class="btn btn-pink-lg w-100 py-3 fw-800 shadow-pink" :disabled="dang_tai">
                <span v-if="dang_tai" class="spinner-border spinner-border-sm me-2"></span>
                {{ dang_tai ? 'Đang kiểm tra...' : 'Vào hệ thống' }}
              </button>
            </form>

            <div class="mt-5 pt-4 border-top text-center text-muted small">
                © 2026 Student Portal. Bảo mật bởi <span class="fw-700 text-accent">Academic Network</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import baseRequestClient from "../../../core/baseRequestClient";
import { authStoreSinhVien } from "../../../core/authStoreSinhVien";

export default {
  name: "StudentLoginPage",
  data() {
    return {
      email: "",
      mat_khau: "",
      hien_mat_khau: false,
      ghi_nho_mat_khau: false,
      dang_tai: false
    };
  },
  created() {
    // Nếu đã có token sinh viên thì vào thẳng dashboard
    if (localStorage.getItem("sinh_vien_token")) {
      this.$router.replace("/sinh-vien");
    }
    const remembered_email = localStorage.getItem("remembered_student_email");
    if (remembered_email) {
      this.email = remembered_email;
      this.ghi_nho_mat_khau = true;
    }
  },
  methods: {
    xu_ly_dang_nhap() {
      this.dang_tai = true;
      const payload = {
        email: this.email,
        mat_khau: this.mat_khau
    };

      baseRequestClient
        .post("login/sinh-vien", payload)
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            // Lưu token và thông tin user sinh viên từ res.data.data
            localStorage.setItem("sinh_vien_token", res.data.data.token);
            authStoreSinhVien.updateUser(res.data.data.user);

            if (this.ghi_nho_mat_khau) {
              localStorage.setItem("remembered_student_email", this.email);
            } else {
              localStorage.removeItem("remembered_student_email");
            }

            this.$router.push("/sinh-vien");
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          const msg = err.response?.data?.message || "Đăng nhập thất bại. Vui lòng kiểm tra lại!";
          this.$toast.error(msg);
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
  min-height: 100vh; display: flex; align-items: center; justify-content: center;
  background-color: #fff9fa; position: relative; overflow: hidden; font-family: 'Inter', sans-serif;
}

.bg-blobs { position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1; }
.blob {
  position: absolute; width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(219,39,119,0.08) 0%, rgba(219,39,119,0) 70%);
  border-radius: 50%; filter: blur(60px);
}
.blob-1 { top: -150px; left: -100px; }
.blob-2 { bottom: -150px; right: -100px; background: radial-gradient(circle, rgba(251,207,232,0.1) 0%, rgba(251,207,232,0) 70%); }

.auth-card {
  width: 100%; max-width: 1100px; background: white; border-radius: 28px;
  box-shadow: 0 25px 50px -12px rgba(131, 24, 67, 0.08); overflow: hidden; z-index: 2;
  border: 1px solid rgba(255,255,255,0.7);
}

.auth-info-panel {
  background: linear-gradient(135deg, #be185d 0%, #db2777 100%);
  position: relative; overflow: hidden;
}

.brand-logo-md {
  width: 60px; height: 60px; background: white; color: #db2777;
  border-radius: 18px; display: flex; align-items: center; justify-content: center;
  font-size: 2rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

.glass-box {
  background: rgba(255,255,255,0.1); backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.2); border-radius: 16px;
}
.icon-circle { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

.brand-logo-sm {
    width: 64px; height: 64px; background: #db2777; color: white;
    border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;
}

.input-group-custom { position: relative; }
.icon-input { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; z-index: 5; }

.btn-toggle-pass {
  position: absolute; right: 18px; top: 50%; transform: translateY(-50%);
  background: none; border: none; color: #94a3b8; font-size: 1.15rem; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; z-index: 5;
}

.flux-input-lg {
  padding: 14px 18px 14px 50px; border-radius: 14px; border: 1px solid #e2e8f0;
  font-size: 1rem; font-weight: 500; transition: all 0.3s; background: #faf5f6;
}
.flux-input-lg:focus { background: white; border-color: #db2777; box-shadow: 0 0 0 4px rgba(219,39,119,0.1); outline: none; }

.custom-check {
    width: 1.1em; height: 1.1em;
    cursor: pointer;
}
.custom-check:checked {
    background-color: #db2777;
    border-color: #db2777;
}

.btn-pink-lg { background: #db2777; color: white; border-radius: 14px; transition: all 0.3s; border: none; }
.btn-pink-lg:hover { background: #be185d; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(219,39,119,0.3); color: white; }

.fw-800 { font-weight: 800; }
.text-accent { color: #db2777 !important; }

@media (max-width: 1024px) {
  .auth-info-panel { display: none; }
  .auth-page { padding: 18px; }
}
</style>
