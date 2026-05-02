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
              <i class="bi bi-mortarboard-fill"></i>
            </div>
            <h1 class="display-6 fw-800 mb-2">Hỗ Trợ<br />Sinh Viên</h1>
            <p class="opacity-75">Hệ thống hỗ trợ khôi phục mật khẩu thông qua Email sinh viên đã được đăng ký.</p>
          </div>
          
          <div class="info-footer">
            <div class="glass-box p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle bg-white text-pink-dark">
                  <i class="bi bi-info-circle"></i>
                </div>
                <div class="small">
                  <div class="fw-700">Lưu ý</div>
                  <div class="opacity-75">Vui lòng kiểm tra kỹ hộp thư Spaim nếu không nhận được link.</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cột phải: Form -->
        <div class="col-lg-7 p-4 p-md-5 bg-white relative">
          <div class="auth-form-container mx-auto w-100" style="max-width: 420px;">
            <div class="mb-5">
              <router-link to="/sinh-vien/login" class="btn-back text-decoration-none mb-4 d-inline-block">
                <i class="bi bi-arrow-left me-2"></i>Quay lại đăng nhập
              </router-link>
              <h2 class="fw-800 text-pink-dark mb-2">Quên mật khẩu?</h2>
              <p class="text-muted small">
                {{ gui_thanh_cong 
                  ? 'Chúng tôi đã gửi hướng dẫn tới Email của bạn. Vui lòng kiểm tra để tiếp tục.' 
                  : 'Vui lòng nhập Email sinh viên của bạn để nhận đường link khôi phục mật khẩu.' }}
              </p>
            </div>

            <!-- Trạng thái 1: Nhập Email -->
            <transition name="fade" mode="out-in">
              <div v-if="!gui_thanh_cong" key="input">
                <div class="mb-4">
                  <label class="form-label fw-700 small text-uppercase opacity-75">Email sinh viên</label>
                  <div class="input-group-custom">
                    <i class="bi bi-envelope icon-input"></i>
                    <input type="email" class="form-control flux-input-lg" v-model="email" placeholder="sv@sinhvien.edu.vn">
                  </div>
                </div>
                <button @click="gui_yeu_cau" class="btn btn-pink-lg w-100 py-3 fw-800 shadow-pink" :disabled="dang_tai">
                  <span v-if="dang_tai" class="spinner-border spinner-border-sm me-2"></span>
                  Gửi yêu cầu khôi phục
                </button>
              </div>

              <!-- Trạng thái 2: Thông báo thành công -->
              <div v-else key="success" class="text-center py-4">
                <div class="success-icon-wrap mb-4 mx-auto">
                    <i class="bi bi-envelope-check-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-800 mb-3 text-pink-dark">Đã gửi Email thành công</h4>
                <p class="text-muted mb-4 small">Đường link khôi phục đã được gửi tới <strong>{{ email }}</strong>. Link có hiệu lực trong 5 phút.</p>
                <button @click="gui_thanh_cong = false" class="btn btn-outline-pink w-100 py-3 fw-700">
                  Thử lại với Email khác
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
  name: "SinhVienQuenMatKhau",
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
        this.$toast.warning("Vui lòng nhập email sinh viên!");
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
  width: 100%; max-width: 1000px; background: white; border-radius: 28px;
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
  font-size: 1.8rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

.glass-box {
  background: rgba(255,255,255,0.1); backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.2); border-radius: 16px;
}
.icon-circle { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }

.btn-back { color: #94a3b8; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; }
.btn-back:hover { color: #db2777; transform: translateX(-4px); }

.input-group-custom { position: relative; }
.icon-input { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; z-index: 5; }

.flux-input-lg {
  padding: 14px 18px 14px 50px; border-radius: 14px; border: 1px solid #e2e8f0;
  font-size: 1rem; font-weight: 500; transition: all 0.3s; background: #faf5f6;
}
.flux-input-lg:focus { background: white; border-color: #db2777; box-shadow: 0 0 0 4px rgba(219,39,119,0.1); outline: none; }

.btn-pink-lg {
  background: #db2777; color: white; border-radius: 14px; transition: all 0.3s; border: none;
}
.btn-pink-lg:hover:not(:disabled) {
  background: #be185d; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(219,39,119,0.3);
}

.btn-outline-pink {
    border: 1.5px solid #db2777; color: #db2777; border-radius: 14px; background: transparent; transition: all 0.3s;
}
.btn-outline-pink:hover { background: #db2777; color: white; }

.text-pink-dark { color: #db2777; }
.fw-800 { font-weight: 800; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter, .fade-leave-to { opacity: 0; }
</style>
