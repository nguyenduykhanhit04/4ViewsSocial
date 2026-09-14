const { allowedOrigins } = require('./env.config');

/**
 * Cấu hình CORS cho Chat Service (HTTP và Socket.IO).
 */
const corsOptions = {
  origin: (origin, callback) => {
    if (!origin || allowedOrigins.includes(origin) || origin.includes('localhost') || origin.includes('127.0.0.1')) {
      callback(null, true);
    } else {
      callback(new Error(`CORS Error: Origin ${origin} bị từ chối.`));
    }
  },
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
};

module.exports = corsOptions;
