import dotenv from 'dotenv';

dotenv.config();

export const config = {
  port: Number(process.env.PORT) || 4000,
  frontendOrigin: process.env.FRONTEND_ORIGIN || 'http://localhost:5173',
  authServiceUrl: process.env.AUTH_SERVICE_URL || 'http://127.0.0.1:8001',
  chatServiceUrl: process.env.CHAT_SERVICE_URL || 'http://127.0.0.1:8002',
  adminServiceUrl: process.env.ADMIN_SERVICE_URL || 'http://127.0.0.1:8003',
  postServiceUrl: process.env.POST_SERVICE_URL || 'http://127.0.0.1:8004',
};
