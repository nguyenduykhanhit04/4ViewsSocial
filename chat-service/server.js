const http = require('http');
const { Server } = require('socket.io');
const connectDB = require('./src/configs/db.config');
const { port, allowedOrigins } = require('./src/configs/env.config');
const createApp = require('./src/app');
const registerChatSockets = require('./src/sockets/chat.socket');

// 1. Kết nối cơ sở dữ liệu MongoDB
connectDB();

// 2. Khởi tạo HTTP Server & Socket.IO
const server = http.createServer();

const io = new Server(server, {
  cors: {
    origin: allowedOrigins,
    methods: ['GET', 'POST'],
    credentials: true,
  },
});

// 3. Đăng ký Express App vào HTTP Server
const app = createApp(io);
server.on('request', app);

// 4. Đăng ký sự kiện Socket.IO
registerChatSockets(io);

// 5. Lắng nghe trên Port
server.listen(port, () => {
  console.log('====================================================');
  console.log(`💬 4ViewsSocial Chat Service is running on port ${port}`);
  console.log(`📡 Socket.IO & Realtime Chat ready`);
  console.log('====================================================');
});