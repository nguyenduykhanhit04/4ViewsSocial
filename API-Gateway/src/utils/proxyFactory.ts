import { Request, Response } from 'express';
import httpProxy from 'http-proxy';

// Tạo instance Proxy (Singleton)
// changeOrigin: true -> Giúp tránh lỗi CORS/Virtual Host ở phía backend
const proxy = httpProxy.createProxyServer({
  changeOrigin: true,
  secure: false, // Chấp nhận https tự ký (nếu môi trường dev)
});

export function createProxyHandler(target: string) {
  return (req: Request, res: Response) => {
    
    // 1. Xử lý lại URL (Path Rewrite) để giống logic cũ của bạn
    // Logic cũ: backendUrl = target + '/api' + (url đã bỏ base)
    const path = req.originalUrl.replace(req.baseUrl, "");
    
    // http-proxy sẽ tự động nối target + req.url
    // Nên ta gán đè req.url thành đường dẫn mong muốn
    req.url = '/api' + path;

    console.log(`→ Proxy [http-proxy] tới [${target}${req.url}]`);

    // 2. Thực hiện Proxy (Chuyển tiếp luồng dữ liệu)
    proxy.web(req, res, {
      target: target,
      // timeout: 60000, // (Tùy chọn) Timeout
    }, (err) => {
      
      // 3. Xử lý lỗi khi Backend chết hoặc không kết nối được
      console.error(`Proxy ERROR [${target}]:`, err.message);

      if (!res.headersSent) {
        res.status(502).json({
          message: "Bad Gateway / Backend unavailable",
          error: err.message,
        });
      }
    });
  };
}