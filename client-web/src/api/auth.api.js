import api from "./http.client";

/**
 * Service quản lý các yêu cầu API liên quan đến xác thực người dùng (Auth Service).
 */
export const authApi = {
  /**
   * Đăng nhập người dùng bằng email và mật khẩu.
   *
   * @param {Object} credentials Thông tin đăng nhập
   * @param {string} credentials.email Email người dùng
   * @param {string} credentials.password Mật khẩu
   * @return {Promise<import("axios").AxiosResponse>} Phản hồi chứa thông tin người dùng và JWT Access Token
   */
  login(credentials) {
    return api.post("/api/auth/login", credentials);
  },

  /**
   * Đăng ký tài khoản người dùng mới.
   *
   * @param {Object} data Thông tin đăng ký
   * @param {string} data.name Tên người dùng
   * @param {string} data.username Tên định danh tài khoản
   * @param {string} data.email Địa chỉ email
   * @param {string} data.password Mật khẩu
   * @return {Promise<import("axios").AxiosResponse>}
   */
  register(data) {
    return api.post("/api/auth/register", data);
  },

  /**
   * Đăng nhập hoặc đăng ký nhanh qua Google OAuth (sử dụng ID Token).
   *
   * @param {Object} payload Chứa Google ID Token
   * @param {string} payload.token ID Token nhận từ Google OAuth SDK
   * @return {Promise<import("axios").AxiosResponse>}
   */
  loginWithGoogle(payload) {
    return api.post("/api/auth/loginwithgoogle", payload);
  },

  /**
   * Đăng xuất người dùng khỏi hệ thống.
   *
   * @param {Object} payload
   * @param {number|string} payload.user_id ID người dùng cần đăng xuất
   * @return {Promise<import("axios").AxiosResponse>}
   */
  logout(payload) {
    return api.post("/api/auth/logout", payload);
  },

  /**
   * Đăng ký hoặc cập nhật FCM Device Token để nhận thông báo đẩy.
   *
   * @param {Object} payload
   * @param {number|string} payload.user_id ID người dùng
   * @param {string} payload.device_token Chuỗi FCM token từ Firebase
   * @return {Promise<import("axios").AxiosResponse>}
   */
  setDeviceToken(payload) {
    return api.post("/api/post/set-device-token", payload);
  },
};

export default authApi;
