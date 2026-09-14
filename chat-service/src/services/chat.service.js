const mongoose = require('mongoose');
const Conversation = require('../models/Conversation');
const Message = require('../models/Message');

class ChatService {
  /**
   * Lấy danh sách các cuộc trò chuyện của một người dùng.
   *
   * @param  {string} userId
   * @return {Promise<Array>}
   */
  async getConversations(userId) {
    return await Conversation.find({
      members: { $in: [String(userId)] },
    })
      .sort({ updated_at: -1 })
      .lean();
  }

  /**
   * Lấy lịch sử tin nhắn của một cuộc hội thoại (sắp xếp tăng dần theo thời gian).
   *
   * @param  {string} conversationId
   * @param  {number} limit
   * @param  {number} offset
   * @return {Promise<Array>}
   */
  async getMessages(conversationId, limit = 50, offset = 0) {
    if (!mongoose.Types.ObjectId.isValid(conversationId)) {
      throw new Error('Mã cuộc trò chuyện (conversation_id) không hợp lệ.');
    }

    const messagesDesc = await Message.find({ conversation_id: conversationId })
      .sort({ created_at: -1 })
      .skip(Number(offset))
      .limit(Number(limit))
      .lean();

    // Đảo ngược mảng để client hiển thị từ trên xuống dưới (Cũ ➔ Mới)
    return messagesDesc.reverse();
  }

  /**
   * Lưu tin nhắn mới và cập nhật bản ghi Last Message của cuộc trò chuyện.
   *
   * @param  {Object} data
   * @return {Promise<Object>}
   */
  async sendMessage(data) {
    const { conversation_id, sender_id, content, attachments, sender_info } = data;

    if (!conversation_id || !sender_id) {
      throw new Error('Thiếu thông tin bắt buộc (conversation_id, sender_id).');
    }

    // 1. Tạo bản ghi Message
    const newMessage = new Message({
      conversation_id,
      sender_id: String(sender_id),
      content: content || '',
      attachments: attachments || [],
      sender_info: sender_info || {},
    });

    await newMessage.save();

    // 2. Cập nhật preview cho cuộc hội thoại
    let previewText = content;
    if (!content && attachments && attachments.length > 0) {
      previewText = `[Đã gửi ${attachments.length} tệp đính kèm]`;
    }

    await Conversation.findByIdAndUpdate(conversation_id, {
      last_message: {
        content: previewText,
        sender: String(sender_id),
        created_at: new Date(),
      },
      updated_at: new Date(),
    });

    return newMessage;
  }

  /**
   * Tạo cuộc trò chuyện mới (hoặc trả về cuộc trò chuyện 1-1 đã có).
   *
   * @param  {Object} param0
   * @return {Promise<{ data: Object, isNew: boolean }>}
   */
  async createConversation({ members, type = 'private', name }) {
    if (!members || members.length < 2) {
      throw new Error('Cuộc trò chuyện cần ít nhất 2 thành viên.');
    }

    const memberStrings = members.map((m) => String(m));

    // Nếu là chat 1-1 riêng tư, kiểm tra xem đã tồn tại chưa
    if (type === 'private') {
      const existing = await Conversation.findOne({
        type: 'private',
        members: { $all: memberStrings, $size: memberStrings.length },
      });

      if (existing) {
        return { data: existing, isNew: false };
      }
    }

    // Tạo mới
    const newConv = new Conversation({
      members: memberStrings,
      type,
      name: name || 'Cuộc trò chuyện',
      last_message: {
        content: 'Bắt đầu cuộc trò chuyện',
        created_at: new Date(),
      },
    });

    await newConv.save();
    return { data: newConv, isNew: true };
  }
}

module.exports = new ChatService();
