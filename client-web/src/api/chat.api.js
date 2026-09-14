import api from "./http.client";

/**
 * Service quản lý các yêu cầu API nhắn tin và hội thoại thời gian thực (Chat Service).
 */
export const chatApi = {
  /**
   * Lấy danh sách hội thoại của người dùng.
   *
   * @param {Object} payload
   * @param {number|string} payload.user_id ID người dùng
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getConversations(payload) {
    return api.post("/api/chat/get-conversations", payload);
  },

  /**
   * Lấy lịch sử tin nhắn của một cuộc hội thoại.
   *
   * @param {Object} payload
   * @param {string} payload.conversation_id ID cuộc hội thoại
   * @param {number} [payload.limit=50] Giới hạn số lượng tin nhắn
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getMessages(payload) {
    return api.post("/api/chat/get-messages", payload);
  },

  /**
   * Gửi một tin nhắn mới vào cuộc hội thoại.
   *
   * @param {Object} payload
   * @param {string} payload.conversation_id ID hội thoại
   * @param {number|string} payload.sender_id ID người gửi
   * @param {string} [payload.message] Nội dung văn bản
   * @param {string} [payload.type='text'] Loại tin nhắn ('text' | 'image' | 'video' | 'file')
   * @param {string} [payload.file_url] Đường dẫn file đính kèm
   * @return {Promise<import("axios").AxiosResponse>}
   */
  sendMessage(payload) {
    return api.post("/api/chat/send-message", payload);
  },

  /**
   * Tạo cuộc hội thoại mới giữa các thành viên.
   *
   * @param {Object} payload
   * @param {Array<number|string>} payload.members Danh sách ID người tham gia
   * @param {string} [payload.type='private'] Loại hội thoại ('private' | 'group')
   * @param {string} [payload.name] Tên nhóm chat (nếu là group)
   * @return {Promise<import("axios").AxiosResponse>}
   */
  createConversation(payload) {
    return api.post("/api/chat/create-conversation", payload);
  },

  /**
   * Lấy danh sách bạn bè gợi ý nhắn tin.
   *
   * @param {Object} payload
   * @param {number|string} payload.user_id
   * @return {Promise<import("axios").AxiosResponse>}
   */
  getSuggestedChatFriends(payload) {
    return api.post("/api/post/suggest-friend-message", payload);
  },
};

export default chatApi;
