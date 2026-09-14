import morgan from 'morgan';

/**
 * Middleware ghi log thông tin các HTTP request trong môi trường phát triển (Development).
 * Ghi lại HTTP Method, URL, Status Code, Thời gian phản hồi.
 */
export const requestLogger = morgan('dev');
