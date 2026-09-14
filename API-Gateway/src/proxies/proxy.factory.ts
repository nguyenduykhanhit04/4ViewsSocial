import { Request, Response } from 'express';
import httpProxy from 'http-proxy';

/**
 * Khởi tạo Proxy Server (Singleton Instance).
 * changeOrigin: true để tránh lỗi CORS và Virtual Host trên máy chủ đích.
 * secure: false cho phép kết nối trong môi trường phát triển (self-signed certs).
 */
const proxy = httpProxy.createProxyServer({
  changeOrigin: true,
  secure: false,
});

/**
 * Tạo một Express Middleware Handler để chuyển tiếp (Proxy) toàn bộ Request sang Microservice đích.
 *
 * @param  targetServiceName  Tên định danh của service đích (ví dụ: 'Auth Service', 'Timeline Service')
 * @param  targetUrl          Địa chỉ URL gốc của service đích (ví dụ: 'http://auth-service:8001')
 * @return Express Request Handler
 */
export function createProxyHandler(targetServiceName: string, targetUrl: string) {
  return (req: Request, res: Response): void => {
    // 1. Rewrite URL: Lấy phần path sau baseUrl và ghép với tiền tố '/api' của Backend
    const subPath = req.originalUrl.replace(req.baseUrl, '');
    req.url = '/api' + subPath;

    console.log(`🔀 [Gateway Proxy] [${req.method}] ${req.originalUrl} ➔ [${targetServiceName}] ${targetUrl}${req.url}`);

    // 2. Chuyển tiếp Request qua luồng Stream nguyên vẹn (Hỗ trợ upload ảnh/video dung lượng lớn)
    proxy.web(
      req,
      res,
      {
        target: targetUrl,
      },
      (err: Error) => {
        // 3. Bắt lỗi khi Microservice đích bị tắt hoặc không phản hồi
        console.error(`🚨 [Gateway Proxy Failed] Không thể kết nối tới [${targetServiceName}] (${targetUrl}):`, err.message);

        if (!res.headersSent) {
          res.status(502).json({
            code: 502,
            message: `Dịch vụ ${targetServiceName} hiện không khả dụng (Bad Gateway). Vui lòng thử lại sau.`,
            error: err.message,
          });
        }
      }
    );
  };
}
