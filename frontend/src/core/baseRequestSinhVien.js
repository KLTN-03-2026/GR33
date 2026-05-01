import axios from "axios";
import { authStoreSinhVien } from "./authStoreSinhVien";

const baseRequestSinhVien = axios.create({
    baseURL: "https://dar-app-dbmal.ondigitalocean.app/api/sinh-vien/",
    // baseURL: "http://127.0.0.1:8000/api/sinh-vien/",
    timeout: 30000,
});

baseRequestSinhVien.interceptors.request.use(
    function (config) {
        const token = localStorage.getItem("sinh_vien_token");
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    function (error) {
        return Promise.reject(error);
    }
);

baseRequestSinhVien.interceptors.response.use(
    (response) => {
        // Tự động đồng bộ Store nếu response có chứa data user (thường từ profile)
        if (response.data && response.data.status && response.data.data && (response.data.data.ma_sinh_vien || response.data.data.email)) {
            authStoreSinhVien.updateUser(response.data.data);
        }
        return response;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default baseRequestSinhVien;
