const mongoose = require('mongoose');

const conversationSchema = new mongoose.Schema({
    members: [{ type: String, required: true }],

    // Thêm tên nhóm (Chat riêng thì để trống cũng được)
    name: { type: String },

    // Thêm admin (Trưởng nhóm) - Để sau này làm chức năng kick thành viên
    admin_id: { type: String },

    type: {
        type: String,
        enum: ['private', 'group'],
        default: 'private'
    },
    last_message: {
        content: String,
        sender: String,
        created_at: Date
    }
}, { timestamps: { createdAt: 'created_at', updatedAt: 'updated_at' } });

module.exports = mongoose.model('Conversation', conversationSchema);