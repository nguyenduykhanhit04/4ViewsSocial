const chatService = require('../services/chat.service');

class ChatController {
  /**
   * Upload tệp tin đính kèm (Ảnh/Video).
   */
  async uploadFiles(req, res) {
    try {
      if (!req.files || req.files.length === 0) {
        return res.status(400).json({ success: false, message: 'Chưa chọn tệp tin tải lên.' });
      }

      const attachments = req.files.map((file) => ({
        url: `${req.protocol}://${req.get('host')}/uploads/${file.filename}`,
        type: file.mimetype.startsWith('image/') ? 'image' : 'video',
      }));

      return res.status(200).json({ success: true, data: attachments });
    } catch (err) {
      return res.status(500).json({ success: false, error: err.message });
    }
  }

  /**
   * Lấy danh sách hội thoại của người dùng.
   */
  async getConversations(req, res) {
    try {
      const userId = req.body.user_id || req.query.user_id;
      if (!userId) {
        return res.status(400).json({ success: false, error: 'Thiếu thông tin user_id.' });
      }

      const conversations = await chatService.getConversations(userId);
      return res.status(200).json({ success: true, data: conversations });
    } catch (err) {
      return res.status(500).json({ success: false, error: err.message });
    }
  }

  /**
   * Lấy lịch sử tin nhắn trong cuộc trò chuyện.
   */
  async getMessages(req, res) {
    try {
      const { conversation_id, limit, offset } = req.body;
      if (!conversation_id) {
        return res.status(400).json({ success: false, error: 'Thiếu conversation_id.' });
      }

      const messages = await chatService.getMessages(conversation_id, limit, offset);
      return res.status(200).json({ success: true, data: messages });
    } catch (err) {
      return res.status(500).json({ success: false, error: err.message });
    }
  }

  /**
   * Gửi tin nhắn mới và phát realtime qua Socket.IO.
   */
  async sendMessage(req, res, io) {
    try {
      const newMessage = await chatService.sendMessage(req.body);

      // Bắn Socket Realtime cho tất cả client trong phòng
      if (io) {
        io.to(String(req.body.conversation_id)).emit('receive_message', newMessage);
      }

      return res.status(200).json({ success: true, data: newMessage });
    } catch (err) {
      return res.status(500).json({ success: false, error: err.message });
    }
  }

  /**
   * Tạo cuộc trò chuyện mới.
   */
  async createConversation(req, res) {
    try {
      const result = await chatService.createConversation(req.body);
      return res.status(200).json({
        success: true,
        data: result.data,
        isNew: result.isNew,
      });
    } catch (err) {
      return res.status(500).json({ success: false, error: err.message });
    }
  }
}

module.exports = new ChatController();
