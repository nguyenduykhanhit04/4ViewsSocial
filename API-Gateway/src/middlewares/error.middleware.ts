import { Request, Response, NextFunction } from 'express';

export function errorHandler(
  err: any,
  req: Request,
  res: Response,
  _next: NextFunction
) {
  console.error('Gateway Error:', err);
  res.status(500).json({ message: 'Internal Gateway Error' });
}
