/**
 * Đăng ký và xử lý các sự kiện Socket.IO thời gian thực cho Chat Service.
 *
 * @param {import('socket.io').Server} io
 */
function registerChatSockets(io) {
  io.on('connection', (socket) => {
    // 1. Tham gia vào phòng chat cụ thể (Room ID = conversation_id)
    socket.on('join_room', (conversationId) => {
      if (conversationId) {
        socket.join(String(conversationId));
      }
    });

    // 2. Rời khỏi phòng chat
    socket.on('leave_room', (conversationId) => {
      if (conversationId) {
        socket.leave(String(conversationId));
      }
    });

    // 3. Sự kiện người dùng đang gõ tin nhắn (Typing Indicator)
    socket.on('typing', ({ conversation_id, user_name }) => {
      socket.to(String(conversation_id)).emit('user_typing', { user_name });
    });

    socket.on('stop_typing', ({ conversation_id }) => {
      socket.to(String(conversation_id)).emit('user_stop_typing');
    });

    // 4. Ngắt kết nối
    socket.on('disconnect', () => {
      // Clean up nếu cần
    });
  });
}

module.exports = registerChatSockets;
