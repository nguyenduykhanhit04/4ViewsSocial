import axios from "axios";

/**
 * Axios HTTP Client instance cấu hình kết nối tới API Gateway.
 * Hỗ trợ tự động gắn JWT Bearer Token và xử lý lỗi phản hồi tập trung.
 */
const httpClient = axios.create({
  baseURL: import.meta.env.VITE_API_GATEWAY_URL || "http://localhost:4000",
  withCredentials: true,
  timeout: 30000,
  headers: {
    "Content-Type": "application/json",
  },
});

/**
 * Request Interceptor: Tự động đính kèm Access Token vào Header Authorization nếu có.
 */
httpClient.interceptors.request.use(
  (config) => {
    const token =
      sessionStorage.getItem("access_token") ||
      sessionStorage.getItem("token") ||
      localStorage.getItem("access_token") ||
      localStorage.getItem("token");
    if (token && !config.headers["Authorization"]) {
      config.headers["Authorization"] = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

/**
 * Response Interceptor: Xử lý tập trung phản hồi và mã lỗi từ backend (401, 403, 500).
 */
httpClient.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response) {
      const { status } = error.response;
      if (status === 401) {
        console.warn("Phiên đăng nhập đã hết hạn hoặc không hợp lệ.");
      } else if (status === 403) {
        console.warn("Bạn không có quyền thực hiện hành động này.");
      }
    }
    return Promise.reject(error);
  }
);

export default httpClient;
