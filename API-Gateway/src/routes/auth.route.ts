import { Router } from "express";
import { createProxyHandler } from "../utils/proxyFactory";
import { config } from "../config/env";

const router = Router();

// All non-order routes → auth service
router.use(createProxyHandler(config.authServiceUrl));

export default router;
