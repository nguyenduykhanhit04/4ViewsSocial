import { Router } from 'express';
import authRouter from './auth.route';
import postRouter from './post.route';
import chatRouter from './chat.route';
import adminRouter from './admin.route';

const router = Router();

/**
 * Đăng ký các nhóm route chính cho API Gateway:
 * - /api/auth   ➔ Auth Service
 * - /api/post   ➔ Timeline Service
 * - /api/posts  ➔ Timeline Service (Hỗ trợ số nhiều)
 * - /api/chat   ➔ Chat Service
 * - /api/admin  ➔ Admin Service
 */
router.use('/auth', authRouter);
router.use('/post', postRouter);
router.use('/posts', postRouter);
router.use('/chat', chatRouter);
router.use('/admin', adminRouter);

export default router;
