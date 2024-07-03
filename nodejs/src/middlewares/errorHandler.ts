import { Request, Response, NextFunction } from 'express';
import { Logger } from '../utils/Logger';

export const errorHandler = (err: unknown, req: Request, res: Response, next: NextFunction) => {
    let errorMessage: string;
    if (err instanceof Error) {
        errorMessage = err.message;
    } else {
        errorMessage = 'An unknown error occurred';
    }
    const logger = Logger.getInstance();
    logger.logError(errorMessage);
    res.status(500).send(errorMessage);
};
