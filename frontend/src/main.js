import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'
import Toaster, { createToaster } from "@meforma/vue-toaster";
import { authStore } from './core/authStore';
import { authStoreSinhVien } from './core/authStoreSinhVien';

const toaster = createToaster({
    position: 'top-right'
});

const app = createApp(App)
app.use(router)
app.use(Toaster, {
    position: 'top-right'
});

export { toaster };

// 1. Cung cấp Auth Store cho toàn bộ app
app.config.globalProperties.$auth = authStore;
app.config.globalProperties.$authSV = authStoreSinhVien;

// 2. Global Permission Helpers - Phản ứng thời gian thực với Auth Store
app.config.globalProperties.$checkPermission = (allowedRoles) => {
    const user = authStore.user;
    if (!user || Object.keys(user).length === 0) return false;
    try {
        const roleId = user.chuc_vu_id || user.chuc_vu?.id;
        return allowedRoles.includes(Number(roleId));
    } catch (e) {
        return false;
    }
};

app.config.globalProperties.$hasPermission = (permissionId) => {
    const user = authStore.user;
    if (!user || Object.keys(user).length === 0) return false;
    try {
        const roleId = Number(user.chuc_vu_id || user.chuc_vu?.id || user.chucVu?.id);
        const permId = Number(permissionId);

        if (roleId === 1) return true;

        const listQuyens = (user.list_quyens || []).map(id => Number(id));
        const chucVuObj = user.chuc_vu || user.chucVu || {};
        const permissions = (chucVuObj.chuc_nangs || chucVuObj.chucNangs || []).map(p => Number(p.id));
        
        return listQuyens.includes(permId) || permissions.includes(permId);
    } catch (e) {
        return false;
    }
};

app.mount('#app')