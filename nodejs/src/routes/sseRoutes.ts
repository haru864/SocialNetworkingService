import { Router, Request, Response, NextFunction } from 'express';
import { SseController } from '../controllers/SseController';
import { RedisService } from '../services/RedisService';

const router = Router();
const sseController = new SseController(new RedisService());

router.get(
    '/sse/notifications',
    (req: Request, res: Response, next: NextFunction) => sseController.handleGetNotificationRequest(req, res, next)
);
router.get(
    '/sse/message',
    (req: Request, res: Response, next: NextFunction) => sseController.handleGetMessageRequest(req, res, next)
);
router.post(
    '/sse/notifications',
    (req: Request, res: Response, next: NextFunction) => sseController.handlePostNotificationRequest(req, res, next)
);
router.post(
    '/sse/message',
    (req: Request, res: Response, next: NextFunction) => sseController.handlePostMessageRequest(req, res, next)
);

export default router;
