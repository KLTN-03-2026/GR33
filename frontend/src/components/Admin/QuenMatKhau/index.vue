<template>
  <div class="auth-page px-3">
    <div class="bg-blobs">
      <div class="blob blob-1"></div>
      <div class="blob blob-2"></div>
    </div>

    <div class="auth-card animate__animated animate__fadeIn">
      <div class="row g-0 h-100">
        <!-- Cột trái: Thông tin -->
        <div class="col-lg-5 d-none d-lg-flex auth-info-panel p-5 flex-column justify-content-between text-white">
          <div class="brand-header">
            <div class="brand-logo-md mb-3">
              <i class="bi bi-key-fill"></i>
            </div>
            <h1 class="display-6 fw-800 mb-2">Khôi Phục<br />Mật Khẩu Admin</h1>
            <p class="opacity-75">Hệ thống bảo mật xác thực tiên tiến đảm bảo an toàn cho tài khoản cán bộ.</p>
          </div>
          
          <div class="info-footer">
            <div class="glass-box p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle bg-white text-rose-dark">
                  <i class="bi bi-shield-check"></i>
                </div>
                <div class="small">
                  <div class="fw-700">Xác thực an toàn</div>
                  <div class="opacity-75">Link khôi phục có hiệu lực trong 5 phút.</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cột phải: Form -->
        <div class="col-lg-7 p-4 p-md-5 bg-white relative">
          <div class="auth-form-container mx-auto w-100" style="max-width: 420px;">
            <div class="mb-5">
              <router-link to="/admin/login" class="btn-back text-decoration-none mb-4 d-inline-block">
                <i class="bi bi-arrow-left me-2"></i>Quay lại đăng nhập
              </router-link>
              <h2 class="fw-800 text-main mb-2">Quên mật khẩu?</h2>
              <p class="text-muted small">
                {{ gui_thanh_cong 
                  ? 'Một email khôi phục đã được gửi đi. Vui lòng kiểm tra hộp thư của bạn.' 
                  : 'Vui lòng nhập Email quản trị để nhận đường link thiết lập lại mật khẩu.' }}
              </p>
            </div>

            <!-- Trạng thái 1: Nhập Email -->
            <transition name="fade" mode="out-in">
              <div v-if="!gui_thanh_cong" key="input">
                <div class="mb-4">
                  <label class="form-label fw-700 small text-uppercase opacity-75">Email khôi phục</label>
                  <div class="input-group-custom">
                    <i class="bi bi-envelope icon-input"></i>
                    <input type="email" class="form-control flux-input-lg" v-model="email" placeholder="admin@hocvien.edu.vn">
                  </div>
                </div>
                <button @click="gui_yeu_cau" class="btn btn-rose-lg w-100 py-3 fw-800 shadow-rose" :disabled="dang_tai">
                  <span v-if="dang_tai" class="spinner-border spinner-border-sm me-2"></span>
                  Gửi yêu cầu khôi phục
                </button>
              </div>

              <!-- Trạng thái 2: Thông báo thành công -->
              <div v-else key="success" class="text-center py-4">
                <div class="success-icon-wrap mb-4 mx-auto">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-800 mb-3">Kiểm tra Email của bạn</h4>
                <p class="text-muted mb-4 small">Chúng tôi đã gửi hướng dẫn đổi mật khẩu tới <strong>{{ email }}</strong>. Link sẽ hết hạn sau 5 phút.</p>
                <button @click="gui_thanh_cong = false" class="btn btn-outline-rose w-100 py-3 fw-700">
                  Gửi lại yêu cầu khác
                </button>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import baseRequestClient from "../../../core/baseRequestClient";

export default {
  name: "AdminQuenMatKhau",
  data() {
    return {
      email: "",
      dang_tai: false,
      gui_thanh_cong: false
    };
  },
  methods: {
    gui_yeu_cau() {
      if (!this.email) {
        this.$toast.warning("Vui lòng nhập email!");
        return;
      }
      this.dang_tai = true;
      baseRequestClient
        .post("forgot-password", { email: this.email })
        .then((res) => {
          if (res.data.status) {
            this.gui_thanh_cong = true;
            this.$toast.success(res.data.message);
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
            const msg = err.response?.data?.message || "Có lỗi hệ thống khi gửi yêu cầu!";
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
.icon-circle { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }

.btn-back { color: #94a3b8; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; }
.btn-back:hover { color: #BE123C; transform: translateX(-4px); }

.input-group-custom { position: relative; }
.icon-input { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; z-index: 5; }

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

.btn-outline-rose {
    border: 1.5px solid #BE123C; color: #BE123C; border-radius: 14px; background: transparent; transition: all 0.3s;
}
.btn-outline-rose:hover { background: #BE123C; color: white; }

.text-rose-dark { color: #BE123C; }
.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter, .fade-leave-to { opacity: 0; }
</style>
