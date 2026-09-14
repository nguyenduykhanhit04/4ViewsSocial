import { Router } from 'express';
import { createProxyHandler } from '../proxies/proxy.factory';
import { config } from '../config/env.config';

const router = Router();

/**
 * Điều hướng toàn bộ các request /api/auth/* sang Auth Service (Port 8001).
 */
router.use(createProxyHandler('Auth Service', config.authServiceUrl));

export default router;
