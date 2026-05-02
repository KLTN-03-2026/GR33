<template>
  <div class="auth-page px-3">
    <div class="bg-blobs">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
    </div>

    <div class="auth-card animate__animated animate__fadeInUp">
      <div class="row g-0 h-100">
        <!-- Cột trái: Thông tin -->
        <div class="col-lg-5 d-none d-lg-flex auth-info-panel p-5 flex-column justify-content-between text-white">
          <div class="brand-header">
            <div class="brand-logo-md mb-3">
              <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h1 class="display-6 fw-800 mb-2">Thiết Lập<br />Mật Khẩu Mới</h1>
            <p class="opacity-75">Vui lòng tạo một mật khẩu mạnh để bảo vệ quyền truy cập quản trị của bạn.</p>
          </div>
        </div>

        <!-- Cột phải: Form -->
        <div class="col-lg-7 p-4 p-md-5 bg-white relative">
          <div class="auth-form-container mx-auto w-100" style="max-width: 420px;">
            <div class="mb-5">
              <h2 class="fw-800 text-main mb-2">Đặt lại mật khẩu</h2>
              <p class="text-muted small">Cập nhật mật khẩu mới cho tài khoản: <br><strong class="text-rose-dark">{{ email }}</strong></p>
            </div>

            <form @submit.prevent="cap_nhat_mat_khau">
              <div class="mb-3">
                <label class="form-label fw-700 small text-uppercase opacity-75">Mật khẩu mới</label>
                <div class="input-group-custom">
                  <i class="bi bi-key icon-input"></i>
                  <input :type="hien_pass1 ? 'text' : 'password'" class="form-control flux-input-lg" 
                         v-model="new_password" placeholder="••••••••">
                  <button type="button" class="btn-toggle-pass" @click="hien_pass1 = !hien_pass1">
                    <i :class="hien_pass1 ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                  </button>
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label fw-700 small text-uppercase opacity-75">Xác nhận mật khẩu</label>
                <div class="input-group-custom">
                  <i class="bi bi-check-circle icon-input"></i>
                  <input :type="hien_pass2 ? 'text' : 'password'" class="form-control flux-input-lg" 
                         v-model="confirm_password" placeholder="••••••••">
                  <button type="button" class="btn-toggle-pass" @click="hien_pass2 = !hien_pass2">
                    <i :class="hien_pass2 ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                  </button>
                </div>
              </div>

              <div v-if="!token" class="alert alert-warning small py-2 mb-4">
                 <i class="bi bi-exclamation-triangle-fill me-2"></i> Không tìm thấy Token xác thực. Vui lòng kiểm tra lại link trong Email.
              </div>

              <button type="submit" class="btn btn-rose-lg w-100 py-3 fw-800 shadow-rose" :disabled="dang_tai || !token">
                <span v-if="dang_tai" class="spinner-border spinner-border-sm me-2"></span>
                Cập nhật mật khẩu mới
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import baseRequestClient from "../../../core/baseRequestClient";

export default {
  name: "AdminDatLaiMatKhau",
  data() {
    return {
      token: "",
      email: "",
      new_password: "",
      confirm_password: "",
      hien_pass1: false,
      hien_pass2: false,
      dang_tai: false
    };
  },
  created() {
    // Lấy token và email từ URL query
    this.token = this.$route.query.token;
    this.email = this.$route.query.email;
    
    if (!this.token) {
        this.$toast.error("Thiếu mã xác thực (Token). Vui lòng sử dụng đường link từ Email!");
    }
  },
  methods: {
    cap_nhat_mat_khau() {
      if (!this.token || !this.email) {
        this.$toast.error("Thông tin xác thực không hợp lệ!");
        return;
      }
      if (!this.new_password || !this.confirm_password) {
        this.$toast.warning("Vui lòng nhập đầy đủ thông tin!");
        return;
      }
      if (this.new_password !== this.confirm_password) {
        this.$toast.error("Mật khẩu xác nhận không khớp!");
        return;
      }
      this.dang_tai = true;
      
      const payload = {
        token: this.token,
        email: this.email,
        mat_khau: this.new_password
      };

      baseRequestClient
        .post("reset-password", payload)
        .then((res) => {
          if (res.data.status) {
            this.$toast.success(res.data.message);
            this.$router.push("/admin/login");
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          console.error(err);
          const listErr = err.response?.data?.errors;
          if (listErr) {
            Object.values(listErr).forEach((error) => {
              this.$toast.error(error[0]);
            });
          } else {
            const msg = err.response?.data?.message || "Có lỗi hệ thống khi cập nhật mật khẩu!";
            this.$toast.error(msg);
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
  min-height: 100vh; display: flex; align-items: center; justify-content: center;
  background-color: #f8fafc; position: relative; overflow: hidden; font-family: 'Inter', sans-serif;
}
.bg-blobs { position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1; }
.blob {
  position: absolute; width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(190,18,60,0.08) 0%, rgba(190,18,60,0) 70%);
  border-radius: 50%; filter: blur(60px);
}
.blob-1 { top: -100px; left: -100px; }
.blob-2 { bottom: -100px; right: -100px; background: radial-gradient(circle, rgba(91,141,239,0.08) 0%, rgba(91,141,239,0) 70%); }

.auth-card {
  width: 100%; max-width: 1000px; background: white; border-radius: 28px;
  box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1); overflow: hidden; z-index: 2;
  border: 1px solid rgba(255,255,255,0.7);
}

.auth-info-panel {
  background: linear-gradient(135deg, #BE123C 0%, #881337 100%);
  position: relative; overflow: hidden;
}
.brand-logo-md {
  width: 60px; height: 60px; background: white; color: #BE123C;
  border-radius: 18px; display: flex; align-items: center; justify-content: center;
  font-size: 1.8rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

.glass-box {
  background: rgba(255,255,255,0.1); backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.2); border-radius: 16px;
}

.input-group-custom { position: relative; }
.icon-input { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; z-index: 5; }

.btn-toggle-pass {
  position: absolute; right: 18px; top: 50%; transform: translateY(-50%);
  background: none; border: none; color: #94a3b8; font-size: 1.1rem; cursor: pointer; z-index: 5;
}

.flux-input-lg {
  padding: 14px 18px 14px 50px; border-radius: 14px; border: 1px solid #e2e8f0;
  font-size: 1rem; font-weight: 500; transition: all 0.3s; background: #f8fafc;
}
.flux-input-lg:focus { background: white; border-color: #BE123C; box-shadow: 0 0 0 4px rgba(190,18,60,0.1); outline: none; }

.btn-rose-lg {
  background: #BE123C; color: white; border-radius: 14px; transition: all 0.3s; border: none;
}
.btn-rose-lg:hover:not(:disabled) {
  background: #9f1239; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(190,18,60,0.3);
}

.text-rose-dark { color: #BE123C; }
.fw-800 { font-weight: 800; }
</style>
