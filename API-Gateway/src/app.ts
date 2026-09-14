import express, { Request, Response } from 'express';
import cors from 'cors';
import { config } from './config/env.config';
import { corsOptions } from './config/cors.config';
import { requestLogger } from './middlewares/logger.middleware';
import { errorHandler, notFoundHandler } from './middlewares/error.middleware';
import apiRoutes from './routes';

const app = express();

// ============================================================================
// 1. GLOBAL MIDDLEWARES (Chạy trước Proxy, không can thiệp Body stream)
// ============================================================================
app.use(requestLogger);
app.use(cors(corsOptions));

// ============================================================================
// 2. MICROSERVICE PROXY ROUTES (Phải đặt trước Body Parsers để giữ nguyên Stream)
// ============================================================================
app.use('/api', apiRoutes);

// ============================================================================
// 3. BODY PARSERS (Chỉ áp dụng cho các route nội bộ của Gateway)
// ============================================================================
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// ============================================================================
// 4. HEALTH CHECK & SYSTEM STATUS
// ============================================================================
app.get('/', (_req: Request, res: Response) => {
  res.json({
    name: '4ViewsSocial API Gateway',
    status: 'ONLINE',
    port: config.port,
    timestamp: new Date().toISOString(),
  });
});

app.get('/health', (_req: Request, res: Response) => {
  res.status(200).json({ status: 'healthy' });
});

// ============================================================================
// 5. ERROR HANDLERS (Đặt ở cuối cùng)
// ============================================================================
app.use(notFoundHandler);
app.use(errorHandler);

export default app;