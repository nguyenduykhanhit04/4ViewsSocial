// import { Request, Response, NextFunction } from 'express';
// import { config } from '../config/env';

// export function apiKeyAuth(req: Request, res: Response, next: NextFunction) {
//   const clientKey = req.headers['x-api-key'];

//   if (!clientKey || clientKey !== config.apiKey) {
//     return res.status(401).json({ message: 'Unauthorized' });
//   }

//   next();
// }
