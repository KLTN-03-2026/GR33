import axios from "axios";

const baseRequestClient = axios.create({
    baseURL: "https://dar-app-dbmal.ondigitalocean.app/api/",
    // baseURL: "http://127.0.0.1:8000/api/",
    timeout: 30000,
});

// Thêm interceptor nếu cần thiết trong tương lai (ví dụ: đính kèm Token nếu có)
baseRequestClient.interceptors.request.use(
    function (config) {
        // Có thể lấy token chung nếu có, nhưng thông thường Login không cần token
        return config;
    },
    function (error) {
        return Promise.reject(error);
    }
);

export default baseRequestClient;
