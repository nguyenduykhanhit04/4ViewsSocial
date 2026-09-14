const http = require('http');
const { Server } = require('socket.io');
const connectDB = require('./src/configs/db.config');
const { port, allowedOrigins } = require('./src/configs/env.config');
const createApp = require('./src/app');
const registerChatSockets = require('./src/sockets/chat.socket');

// 1. Kết nối cơ sở dữ liệu MongoDB
connectDB();

// 2. Tạo proxy object cho io trước khi khởi tạo HTTP server
let ioInstance;
const ioProxy = {
  to(room) {
    return {
      emit(event, data) {
        if (ioInstance) ioInstance.to(room).emit(event, data);
      },
    };
  },
  emit(event, data) {
    if (ioInstance) ioInstance.emit(event, data);
  },
};

// 3. Khởi tạo Express App với ioProxy
const app = createApp(ioProxy);

// 4. Khởi tạo HTTP Server bọc Express App
const server = http.createServer(app);

// 5. Khởi tạo Socket.IO đính kèm vào HTTP Server
const io = new Server(server, {
  cors: {
    origin: (origin, callback) => {
      callback(null, true);
    },
    methods: ['GET', 'POST'],
    credentials: true,
  },
});
ioInstance = io;

// 6. Đăng ký sự kiện Socket.IO
registerChatSockets(io);

// 7. Lắng nghe trên Port
server.listen(port, () => {
  console.log('====================================================');
  console.log(`💬 4ViewsSocial Chat Service is running on port ${port}`);
  console.log(`📡 Socket.IO & Realtime Chat ready`);
  console.log('====================================================');
});