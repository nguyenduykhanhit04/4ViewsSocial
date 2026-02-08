import express, { Request, Response } from 'express';
import cors from 'cors';
import morgan from 'morgan';
import { config } from './config/env';
import apiRoutes from './routes';

const app = express();

// ==========================================
// 1. MIDDLEWARE KHÔNG ĐỤNG VÀO BODY (Chạy trước)
// ==========================================

// Logger
app.use(morgan('dev'));

// CORS (Phải ở đầu tiên để trình duyệt không chặn)
app.use(
  cors({
    origin: config.frontendOrigin,
    credentials: true,
  }),
);

console.log('==== GATEWAY CONFIG =====');
console.log('PORT:', config.port);
console.log('FRONTEND_ORIGIN:', config.frontendOrigin);
console.log('ADMIN_SERVICE_URL:', config.adminServiceUrl);
console.log('AUTH_SERVICE_URL:', config.authServiceUrl);
console.log('POST_SERVICE_URL:', config.postServiceUrl);
console.log('CHAT_SERVICE_URL:', config.chatServiceUrl);
console.log('=========================');

// ==========================================
// 2. PROXY ROUTES (QUAN TRỌNG: PHẢI Ở ĐÂY)
// ==========================================
// Đặt Proxy ở đây để nó nhận luồng dữ liệu (Stream) nguyên vẹn
// giúp upload file video/ảnh thành công.
app.use('/api', apiRoutes);

// ==========================================
// 3. BODY PARSERS (Đặt ở dưới cùng)
// ==========================================
// Chỉ chạy nếu request không rơi vào Proxy ở trên
// (Dùng cho các route nội bộ của Gateway nếu có)
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// ==========================================
// 4. OTHER ROUTES
// ==========================================

app.get('/', (req: Request, res: Response) => {
  res.send('API Gateway OK');
});

export default app;