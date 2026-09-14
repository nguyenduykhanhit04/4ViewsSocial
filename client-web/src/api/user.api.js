import api from "./http.client";

/**
 * Service quản lý thông tin tài khoản người dùng, trang cá nhân, theo dõi, tìm kiếm và thông báo.
 */
export const userApi = {
  /**
   * Lấy dữ liệu chi tiết trang cá nhân (Profile) của người dùng.
   *
   * @param {Object} params
   * @param {number|string} params.user_id ID người dùng cần xem
   * @param {number} [params.offset]
   * @param {number} [params.limit]
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getProfile(params) {
    return api.get("/api/post/get-profile", { params });
  },

  /**
   * Lấy danh sách bài viết đã đăng của người dùng.
   *
   * @param {Object} params
   * @param {number|string} params.user_id
   * @param {number} [params.offset]
   * @param {number} [params.limit]
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getUserPosts(params) {
    return api.get("/api/post/get-post-user", { params });
  },

  /**
   * Lấy danh sách các bài viết đã lưu của người dùng.
   *
   * @param {Object} params
   * @param {number|string} params.user_id
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getSavedPosts(params) {
    return api.get("/api/post/get-post-saved", { params });
  },

  /**
   * Cập nhật thông tin hồ sơ người dùng (avatar, bio, tên).
   *
   * @param {FormData|Object} data
   * @return {Promise<import("axios").AxiosResponse>}
   */
  updateProfile(data) {
    return api.post("/api/post/update-profile", data);
  },

  /**
   * Đổi mật khẩu tài khoản người dùng.
   *
   * @param {Object} payload
   * @param {number|string} payload.user_id
   * @param {string} payload.old_password Mật khẩu cũ
   * @param {string} payload.new_password Mật khẩu mới
   * @return {Promise<import("axios").AxiosResponse>}
   */
  changePassword(payload) {
    return api.post("/api/post/change-password", payload);
  },

  /**
   * Lấy danh sách bạn bè gợi ý kết nối.
   *
   * @param {Object} params
   * @param {number|string} params.user_id
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getSuggestedFriends(params) {
    return api.get("/api/post/suggest-friend", { params });
  },

  /**
   * Thực hiện theo dõi hoặc hủy theo dõi một tài khoản khác.
   *
   * @param {Object} payload
   * @param {number|string} payload.user_id ID người dùng thực hiện follow
   * @param {number|string} payload.target_user_id ID người dùng được follow
   * @return {Promise<import("axios").AxiosResponse>}
   */
  followUser(payload) {
    return api.post("/api/post/follow", payload);
  },

  /**
   * Tìm kiếm người dùng theo từ khóa.
   *
   * @param {Object} payload
   * @param {string} payload.keyword
   * @return {Promise<import("axios").AxiosResponse>}
   */
  searchUsers(payload) {
    return api.post("/api/post/search-user", payload);
  },

  /**
   * Lấy thông tin tóm tắt của danh sách người dùng theo mảng ID.
   *
   * @param {Object} payload
   * @param {Array<number|string>} payload.user_ids
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getUsersInfo(payload) {
    return api.post("/api/post/get-users-info", payload);
  },

  /**
   * Lấy danh sách thông báo của người dùng.
   *
   * @param {Object} params
   * @param {number|string} params.user_id
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getNotifications(params) {
    return api.get("/api/post/notifications", { params });
  },

  /**
   * Đánh dấu toàn bộ thông báo là đã đọc.
   *
   * @param {Object} payload
   * @param {number|string} payload.user_id
   * @return {Promise<import("axios").AxiosResponse>}
   */
  markNotificationsRead(payload) {
    return api.post("/api/post/notifications/mark-read", payload);
  },

  /**
   * Đánh dấu một thông báo cụ thể là đã đọc.
   *
   * @param {number|string} notificationId
   * @return {Promise<import("axios").AxiosResponse>}
   */
  markSingleNotificationRead(notificationId) {
    return api.post(`/api/post/notifications/mark-read/${notificationId}`);
  },
};

export default userApi;
