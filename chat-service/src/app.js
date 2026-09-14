const express = require('express');
const cors = require('cors');
const path = require('path');
const corsOptions = require('./configs/cors.config');
const createChatRouter = require('./routes/chat.routes');

/**
 * Tạo Express Application cho Chat Service.
 *
 * @param {import('socket.io').Server} io
 * @return {express.Application}
 */
function createApp(io) {
  const app = express();

  // 1. Middlewares
  app.use(cors(corsOptions));
  app.use(express.json());
  app.use(express.urlencoded({ extended: true }));

  // 2. Static files (Uploads)
  app.use('/uploads', express.static(path.join(__dirname, '../uploads')));

  // 3. API Routes
  app.use('/api', createChatRouter(io));
  app.use('/api/chat', createChatRouter(io)); // Hỗ trợ cả 2 path

  // 4. Health Check
  app.get('/', (_req, res) => {
    res.json({
      service: '4ViewsSocial Chat Service',
      status: 'ONLINE',
      timestamp: new Date().toISOString(),
    });
  });

  return app;
}

module.exports = createApp;
