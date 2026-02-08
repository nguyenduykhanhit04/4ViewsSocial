import express from 'express';
import axios from 'axios';
import { config } from '../config/env';
import { redis } from '../redis';

const router = express.Router();

/**
 * Dashboard tổng
 */
router.get('/stats', async (req, res) => {
  try {
    // 1️⃣ Gọi Admin Backend (Laravel)
    const adminRes = await axios.get(
      `${config.adminServiceUrl}/api/admin/dashboard/stats`
    );

    // 2️⃣ Đếm user online từ Redis
    const keys = await redis.keys('user:online:*');
    const onlineUsers = keys.length;

    // 3️⃣ Trả về cho frontend
    res.json({
      ...adminRes.data,
      online_users: onlineUsers,
    });
  } catch (error) {
    console.error('Dashboard error:', error);
    res.status(500).json({ message: 'Dashboard API error' });
  }
});

export default router;
