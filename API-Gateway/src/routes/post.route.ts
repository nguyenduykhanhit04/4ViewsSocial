import { Router } from "express";
import { createProxyHandler } from "../utils/proxyFactory";
import { config } from "../config/env";

const router = Router();

// All non-order routes → post service
router.use(createProxyHandler(config.postServiceUrl));

export default router;
