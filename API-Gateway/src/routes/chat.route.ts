import { Router } from 'express';
import { createProxyHandler } from '../proxies/proxy.factory';
import { config } from '../config/env.config';

const router = Router();

/**
 * Điều hướng toàn bộ các request /api/chat/* sang Chat Service (Port 8002).
 */
router.use(createProxyHandler('Chat Service', config.chatServiceUrl));

export default router;
