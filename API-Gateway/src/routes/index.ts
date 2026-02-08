import { Router } from "express";
import adminRouter from "./admin.route";
import authRouter from "./auth.route";
import chatRouter from "./chat.route";
import postRouter from "./post.route";
const router = Router();

router.use("/admin", adminRouter);
router.use("/auth", authRouter);
router.use("/chat", chatRouter);
router.use("/post", postRouter);

export default router;
