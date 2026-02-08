import { Router } from "express";
import { createProxyHandler } from "../utils/proxyFactory";
import { config } from "../config/env";

const router = Router();

// All non-order routes → chat service
router.use(createProxyHandler(config.chatServiceUrl));

export default router;
