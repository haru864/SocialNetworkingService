import { Logger } from "../utils/Logger";
import { Request, Response, NextFunction } from 'express';

export const loggerMiddleware = (req: Request, res: Response, next: NextFunction) => {

    const logger = Logger.getInstance();
    logger.logRequest(req);

    // Check if this is an SSE request
    if (req.headers.accept && req.headers.accept === 'text/event-stream') {
        // For SSE, use a different approach
        res.on('close', () => {
            logger.logResponse(res);
        });
    } else {
        res.on('finish', () => {
            logger.logResponse(res);
        });
    }

    next();
};
