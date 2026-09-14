import api from "./http.client";

/**
 * Service quản lý các yêu cầu API dòng thời gian (Bảng tin, Bài viết, Story, Khám phá).
 */
export const timelineApi = {
  /**
   * Lấy danh sách bài viết trên News Feed của người dùng.
   *
   * @param {Object} params Tham số truy vấn
   * @param {number|string} params.user_id ID người dùng đang đăng nhập
   * @param {number} [params.offset] Vị trí bắt đầu
   * @param {number} [params.limit] Số lượng bài viết
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getPosts(params) {
    return api.get("/api/post/list-post", { params });
  },

  /**
   * Tạo một bài viết mới (kèm file hình ảnh/video nếu có).
   *
   * @param {FormData} formData Đối tượng FormData chứa nội dung và media
   * @return {Promise<import("axios").AxiosResponse>}
   */
  createPost(formData) {
    return api.post("/api/post/add-post", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
  },

  /**
   * Thích hoặc bỏ thích một bài viết.
   *
   * @param {Object} payload Dữ liệu tương tác
   * @param {number|string} payload.post_id ID bài viết
   * @param {number|string} payload.user_id ID người dùng
   * @param {boolean} payload.is_liked Trạng thái đã thích hay chưa
   * @return {Promise<import("axios").AxiosResponse>}
   */
  likePost(payload) {
    return api.post("/api/post/like-post", payload);
  },

  /**
   * Lưu hoặc bỏ lưu bài viết vào bộ sưu tập cá nhân.
   *
   * @param {Object} payload
   * @param {number|string} payload.post_id ID bài viết
   * @param {number|string} payload.user_id ID người dùng
   * @return {Promise<import("axios").AxiosResponse>}
   */
  savePost(payload) {
    return api.post("/api/post/save-post", payload);
  },

  /**
   * Lấy danh sách bình luận của một bài viết cụ thể.
   *
   * @param {Object} params
   * @param {number|string} params.post_id ID bài viết
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getComments(params) {
    return api.get("/api/post/list-comment", { params });
  },

  /**
   * Đăng bình luận mới vào bài viết.
   *
   * @param {Object} payload
   * @param {number|string} payload.post_id ID bài viết
   * @param {number|string} payload.user_id ID người bình luận
   * @param {string} payload.content Nội dung bình luận
   * @param {number|string} [payload.parent_id] ID bình luận cha (nếu là reply)
   * @return {Promise<import("axios").AxiosResponse>}
   */
  addComment(payload) {
    return api.post("/api/post/comment", payload);
  },

  /**
   * Lấy danh sách Story 24h của bạn bè và người theo dõi.
   *
   * @param {Object} params
   * @param {number|string} params.user_id ID người dùng
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getStories(params) {
    return api.get("/api/post/list-story", { params });
  },

  /**
   * Tạo Story 24h mới (ảnh hoặc video ngắn).
   *
   * @param {FormData} formData
   * @return {Promise<import("axios").AxiosResponse>}
   */
  createStory(formData) {
    return api.post("/api/post/add-story", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
  },

  /**
   * Thả tim/thích Story 24h.
   *
   * @param {Object} payload
   * @param {number|string} payload.story_id ID story
   * @param {number|string} payload.user_id ID người dùng
   * @param {boolean} payload.is_liked
   * @return {Promise<import("axios").AxiosResponse>}
   */
  likeStory(payload) {
    return api.post("/api/post/like-story", payload);
  },

  /**
   * Phản hồi Story 24h thông qua tin nhắn.
   *
   * @param {Object} payload
   * @param {number|string} payload.story_id ID story
   * @param {number|string} payload.user_id ID người dùng
   * @param {string} payload.content Nội dung phản hồi
   * @return {Promise<import("axios").AxiosResponse>}
   */
  replyStory(payload) {
    return api.post("/api/post/reply-story", payload);
  },

  /**
   * Lấy danh sách bài viết khám phá (Explore Page) dựa trên độ tương tác.
   *
   * @param {Object} params
   * @param {number|string} params.user_id
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getExplorePosts(params) {
    return api.get("/api/post/explore", { params });
  },
};

export default timelineApi;
