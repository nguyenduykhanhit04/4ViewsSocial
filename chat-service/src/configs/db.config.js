const mongoose = require('mongoose');
const { mongoUri } = require('./env.config');

/**
 * Khởi tạo kết nối cơ sở dữ liệu MongoDB (Mongoose).
 *
 * @return {Promise<void>}
 */
const connectDB = async () => {
  try {
    await mongoose.connect(mongoUri);
    console.log('🍃 [MongoDB] Kết nối cơ sở dữ liệu Chat thành công!');
  } catch (err) {
    console.error('❌ [MongoDB Error] Không thể kết nối cơ sở dữ liệu:', err.message);
    // Trong môi trường dev không throw crash để container tiếp tục retry kết nối
  }
};

module.exports = connectDB;
