const mongoose = require('mongoose');

/**
 * Mongoose Schema đại diện cho Cuộc trò chuyện (1-1 hoặc Nhóm).
 */
const conversationSchema = new mongoose.Schema(
  {
    /** Danh sách user_id thành viên tham gia hội thoại (ID từ MySQL dạng String) */
    members: [{ type: String, required: true }],

    /** Tên cuộc trò chuyện / nhóm chat */
    name: { type: String, default: 'Cuộc trò chuyện' },

    /** ID người tạo / quản trị viên nhóm */
    admin_id: { type: String },

    /** Loại hội thoại: 'private' (1-1) hoặc 'group' (nhóm) */
    type: {
      type: String,
      enum: ['private', 'group'],
      default: 'private',
    },

    /** Bản ghi nhanh tin nhắn cuối cùng để hiển thị trên danh sách */
    last_message: {
      content: { type: String, default: '' },
      sender: { type: String },
      created_at: { type: Date, default: Date.now },
    },
  },
  {
    timestamps: { createdAt: 'created_at', updatedAt: 'updated_at' },
  }
);

module.exports = mongoose.model('Conversation', conversationSchema);