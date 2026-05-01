import axios from "axios";
import { toaster } from "../main";
import { authStore } from "./authStore";

const baseRequestAdmin = axios.create({
    baseURL: "https://dar-app-dbmal.ondigitalocean.app/api/admin/",
    // baseURL: "http://127.0.0.1:8000/api/admin/",
    timeout: 30000,
});

baseRequestAdmin.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("nhan_vien_token");
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

baseRequestAdmin.interceptors.response.use(
    (response) => {
        // Kiểm tra nếu response có chứa thông tin user và quyền hạn (thường từ profile hoặc login)
        if (response.data && response.data.status && response.data.data && response.data.data.list_quyens) {
            const newList = response.data.data.list_quyens;
            const userStr = localStorage.getItem("nhan_vien_user");
            
            if (userStr) {
                try {
                    const user = JSON.parse(userStr);
                    const oldList = user.list_quyens || [];
                    
                    const sOld = JSON.stringify([...oldList].sort());
                    const sNew = JSON.stringify([...newList].sort());
                    
                    if (sOld !== sNew) {
                        // Tự động cập nhật store và localStorage
                        authStore.updateUser(response.data.data);
                        
                        toaster.info("Quyền hạn của bạn đã được cập nhật tự động!", {
                            duration: 5000
                        });
                    }
                } catch (e) {
                    // Falls back to manual update if needed
                }
            } else {
                // Nếu chưa có user trong storage (ví dụ profile call sau khi F5), cập nhật luôn
                authStore.updateUser(response.data.data);
            }
        }
        return response;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default baseRequestAdmin;
