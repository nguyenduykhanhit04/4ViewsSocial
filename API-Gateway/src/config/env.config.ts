import dotenv from 'dotenv';

dotenv.config();

/**
 * Interface định nghĩa cấu trúc cấu hình biến môi trường cho API Gateway.
 */
export interface GatewayConfig {
  /** Cổng chạy API Gateway (Mặc định: 4000) */
  port: number;
  /** Địa chỉ origin của Frontend được phép kết nối (CORS) */
  frontendOrigin: string;
  /** URL dịch vụ xác thực người dùng (Auth Service) */
  authServiceUrl: string;
  /** URL dịch vụ chat thời gian thực (Chat Service) */
  chatServiceUrl: string;
  /** URL dịch vụ quản trị hệ thống (Admin Service) */
  adminServiceUrl: string;
  /** URL dịch vụ bảng tin / bài viết (Timeline / Post Service) */
  postServiceUrl: string;
}

/**
 * Đối tượng cấu hình hệ thống API Gateway.
 */
export const config: GatewayConfig = {
  port: Number(process.env.PORT) || 4000,
  frontendOrigin: process.env.FRONTEND_ORIGIN || 'http://localhost:5173',
  authServiceUrl: process.env.AUTH_SERVICE_URL || 'http://127.0.0.1:8001',
  chatServiceUrl: process.env.CHAT_SERVICE_URL || 'http://127.0.0.1:8002',
  adminServiceUrl: process.env.ADMIN_SERVICE_URL || 'http://127.0.0.1:8003',
  postServiceUrl: process.env.POST_SERVICE_URL || 'http://127.0.0.1:8004',
};
