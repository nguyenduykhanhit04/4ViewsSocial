require('dotenv').config();

/**
 * Cấu hình biến môi trường của Chat Service.
 */
module.exports = {
  port: process.env.PORT || 8002,
  mongoUri: process.env.MONGO_URI || 'mongodb://127.0.0.1:27017/4viewsocial_chat',
  allowedOrigins: [
    'http://localhost:5173',
    'http://localhost:4000',
    'http://localhost:3000',
    'http://127.0.0.1:5173',
    'http://127.0.0.1:4000',
  ],
};
