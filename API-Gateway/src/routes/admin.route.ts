import { Router } from "express";
import { createProxyHandler } from "../utils/proxyFactory";
import { config } from "../config/env";

const router = Router();

// All non-order routes → admin service
router.use(createProxyHandler(config.adminServiceUrl));

export default router;
