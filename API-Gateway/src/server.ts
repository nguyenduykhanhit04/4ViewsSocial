import app from './app';
import { config } from './config/env.config';

const PORT = config.port;

/**
 * Khởi động HTTP Server cho API Gateway.
 */
const server = app.listen(PORT, () => {
  console.log('====================================================');
  console.log(`🚀 4ViewsSocial API Gateway is running on port ${PORT}`);
  console.log(`🌐 Base URL: http://localhost:${PORT}`);
  console.log(`🔗 Allowed Frontend: ${config.frontendOrigin}`);
  console.log('====================================================');
});

export default server;
