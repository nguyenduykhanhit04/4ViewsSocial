import api from "./http.client";

/**
 * Service quản lý các yêu cầu API trang quản trị hệ thống (Admin Portal).
 */
export const adminApi = {
  /**
   * Lấy số liệu thống kê tổng quan (Dashboard stats).
   *
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getDashboardStats() {
    return api.get("/api/admin/dashboard");
  },

  /**
   * Lấy danh sách toàn bộ người dùng trong hệ thống (quản lý người dùng).
   *
   * @param {Object} [params]
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getUsers(params) {
    return api.get("/api/admin/users", { params });
  },

  /**
   * Cập nhật trạng thái hoặc xóa/khóa tài khoản người dùng.
   *
   * @param {number|string} id ID người dùng
   * @return {Promise<import("axios").AxiosResponse>}
   */
  deleteUser(id) {
    return api.delete(`/api/admin/users/${id}`);
  },

  /**
   * Thay đổi trạng thái tài khoản (khóa / mở khóa).
   *
   * @param {Object} payload
   * @param {number|string} payload.user_id
   * @param {string|number} payload.status
   * @return {Promise<import("axios").AxiosResponse>}
   */
  toggleUserStatus(payload) {
    return api.post("/api/admin/users/status", payload);
  },

  /**
   * Lấy danh sách toàn bộ bài viết quản trị.
   *
   * @param {Object} [params]
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getPosts(params) {
    return api.get("/api/admin/posts", { params });
  },

  /**
   * Xóa bài viết vi phạm bởi Admin.
   *
   * @param {number|string} id ID bài viết
   * @return {Promise<import("axios").AxiosResponse>}
   */
  deletePost(id) {
    return api.delete(`/api/admin/posts/${id}`);
  },

  /**
   * Lấy danh sách tin 24h (Stories) quản trị.
   *
   * @param {Object} [params]
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getStories(params) {
    return api.get("/api/admin/stories", { params });
  },

  /**
   * Xóa Story vi phạm bởi Admin.
   *
   * @param {number|string} id ID story
   * @return {Promise<import("axios").AxiosResponse>}
   */
  deleteStory(id) {
    return api.delete(`/api/admin/stories/${id}`);
  },

  /**
   * Lấy danh sách toàn bộ bình luận.
   *
   * @param {Object} [params]
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getComments(params) {
    return api.get("/api/admin/comments", { params });
  },

  /**
   * Xóa bình luận vi phạm bởi Admin.
   *
   * @param {number|string} id ID bình luận
   * @return {Promise<import("axios").AxiosResponse>}
   */
  deleteComment(id) {
    return api.delete(`/api/admin/comments/${id}`);
  },

  /**
   * Lấy danh sách báo cáo vi phạm từ người dùng.
   *
   * @param {Object} [params]
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getReports(params) {
    return api.get("/api/admin/reports", { params });
  },
};

export default adminApi;
