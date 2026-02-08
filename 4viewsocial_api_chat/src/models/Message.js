// src/models/Message.js
const mongoose = require('mongoose');

const messageSchema = new mongoose.Schema({
    conversation_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Conversation', required: true },
    
    // ID lấy từ MySQL (Ví dụ: "1001") -> Dùng để định danh chính xác
    sender_id: { type: String, required: true },

    // 🔥 NÂNG CẤP: Lưu kèm thông tin hiển thị (Snapshot)
    // Để frontend nhận tin nhắn là hiện được ngay Avatar + Tên
    sender_info: {
        name: { type: String },    // VD: "Nguyễn Văn A"
        avatar: { type: String }   // VD: "https://myserver.com/uploads/avatar1.jpg"
    },
    
    content: { type: String, default: "" },

    attachments: [
        {
            url: String,
            type: { 
                type: String, 
                enum: ['image', 'video', 'file'],
                default: 'image'
            }
        }
    ]
}, { timestamps: { createdAt: 'created_at', updatedAt: 'updated_at' } });

module.exports = mongoose.model('Message', messageSchema);