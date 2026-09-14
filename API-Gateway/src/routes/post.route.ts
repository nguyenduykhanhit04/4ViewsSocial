import { Router } from 'express';
import { createProxyHandler } from '../proxies/proxy.factory';
import { config } from '../config/env.config';

const router = Router();

/**
 * Điều hướng toàn bộ các request /api/post/* và /api/posts/* sang Timeline / Post Service (Port 8004).
 */
router.use(createProxyHandler('Timeline Service', config.postServiceUrl));

export default router;
