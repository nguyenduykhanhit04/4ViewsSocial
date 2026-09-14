import { Request, Response, NextFunction } from 'express';

/**
 * Middleware bắt lỗi 404 (Không tìm thấy Route).
 *
 * @param req  Đối tượng Express Request
 * @param res  Đối tượng Express Response
 */
export function notFoundHandler(req: Request, res: Response): void {
  res.status(404).json({
    code: 404,
    message: `Đường dẫn [${req.method}] ${req.originalUrl} không tồn tại trên API Gateway.`,
  });
}

/**
 * Middleware xử lý lỗi tập trung trên API Gateway (Internal Gateway Error).
 *
 * @param err   Lỗi phát sinh
 * @param req   Đối tượng Express Request
 * @param res   Đối tượng Express Response
 * @param _next Đối tượng NextFunction
 */
export function errorHandler(
  err: any,
  req: Request,
  res: Response,
  _next: NextFunction
): void {
  console.error(`❌ [API Gateway Error] [${req.method}] ${req.originalUrl}:`, err);

  if (!res.headersSent) {
    res.status(500).json({
      code: 500,
      message: 'Lỗi máy chủ nội bộ trên API Gateway.',
      error: process.env.NODE_ENV === 'development' ? err.message : undefined,
    });
  }
}
