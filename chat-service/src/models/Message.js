const mongoose = require('mongoose');

/**
 * Mongoose Schema đại diện cho một Tin nhắn trong cuộc trò chuyện.
 */
const messageSchema = new mongoose.Schema(
  {
    /** ID của cuộc hội thoại chứa tin nhắn này */
    conversation_id: {
      type: mongoose.Schema.Types.ObjectId,
      ref: 'Conversation',
      required: true,
      index: true,
    },

    /** ID người gửi (dạng chuỗi String map từ ID MySQL) */
    sender_id: { type: String, required: true },

    /** Snapshot thông tin người gửi (Tên & Avatar để hiển thị ngay lập tức) */
    sender_info: {
      name: { type: String },
      avatar: { type: String },
    },

    /** Nội dung văn bản của tin nhắn */
    content: { type: String, default: '' },

    /** Danh sách tệp tin đính kèm (Ảnh / Video / Tệp) */
    attachments: [
      {
        url: { type: String, required: true },
        type: {
          type: String,
          enum: ['image', 'video', 'file'],
          default: 'image',
        },
      },
    ],

    /** Trạng thái đã xem */
    is_read: { type: Boolean, default: false },
  },
  {
    timestamps: { createdAt: 'created_at', updatedAt: 'updated_at' },
  }
);

module.exports = mongoose.model('Message', messageSchema);