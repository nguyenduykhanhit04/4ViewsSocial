import { Router } from 'express';
import { createProxyHandler } from '../proxies/proxy.factory';
import { config } from '../config/env.config';

const router = Router();

/**
 * Điều hướng toàn bộ các request /api/admin/* sang Admin Service (Port 8003).
 */
router.use(createProxyHandler('Admin Service', config.adminServiceUrl));

export default router;
