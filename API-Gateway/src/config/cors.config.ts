import { CorsOptions } from 'cors';
import { config } from './env.config';

/**
 * Cấu hình bảo mật Cross-Origin Resource Sharing (CORS) cho API Gateway.
 * Cho phép các request từ client được định nghĩa trong frontendOrigin và hỗ trợ gửi cookie/credentials.
 */
export const corsOptions: CorsOptions = {
  origin: (origin, callback) => {
    // Cho phép các request không có origin (như curl, mobile apps, Postman) hoặc khớp với Frontend Origin
    if (!origin || origin === config.frontendOrigin || origin.includes('localhost') || origin.includes('127.0.0.1')) {
      callback(null, true);
    } else {
      callback(new Error(`CORS Error: Origin ${origin} không được phép truy cập.`));
    }
  },
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept', 'Origin'],
};
