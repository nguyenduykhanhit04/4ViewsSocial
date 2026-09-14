const express = require('express');
const chatController = require('../controllers/chat.controller');
const upload = require('../middlewares/upload.middleware');

/**
 * Khởi tạo Chat Router với instance Socket.IO.
 *
 * @param {import('socket.io').Server} io
 * @return {express.Router}
 */
function createChatRouter(io) {
  const router = express.Router();

  // 1. Upload ảnh / video
  router.post('/upload', upload.array('files', 5), (req, res) => chatController.uploadFiles(req, res));

  // 2. Lấy danh sách hội thoại
  router.post('/get-conversations', (req, res) => chatController.getConversations(req, res));
  router.get('/conversations', (req, res) => chatController.getConversations(req, res)); // RESTful alias

  // 3. Lấy lịch sử tin nhắn
  router.post('/get-messages', (req, res) => chatController.getMessages(req, res));
  router.post('/messages', (req, res) => chatController.getMessages(req, res)); // RESTful alias

  // 4. Gửi tin nhắn
  router.post('/send-message', (req, res) => chatController.sendMessage(req, res, io));
  router.post('/message', (req, res) => chatController.sendMessage(req, res, io)); // RESTful alias

  // 5. Tạo cuộc trò chuyện mới
  router.post('/create-conversation', (req, res) => chatController.createConversation(req, res));
  router.post('/conversation', (req, res) => chatController.createConversation(req, res)); // RESTful alias

  return router;
}

module.exports = createChatRouter;
