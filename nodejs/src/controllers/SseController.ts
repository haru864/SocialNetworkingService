import { Request, Response, NextFunction } from 'express';
import { RedisService } from '../services/RedisService';
import { Logger } from '../utils/Logger';
import { GetNotificationRequest } from '../http/request/GetNotificationRequest';
import { RequestClient } from '../models/RequestClient';
import { GetMessageRequest } from '../http/request/GetMessageRequest';
import { PostNotificationRequest } from '../http/request/PostNotificationRequest';
import { PostMessageRequest } from '../http/request/PostMessageRequest';

export class SseController {

    private requestClients: RequestClient[] = [];
    private logger: Logger = Logger.getInstance();
    private redisService: RedisService;

    constructor(redisService: RedisService) {
        this.redisService = redisService;
    }

    public handleGetNotificationRequest(req: Request, res: Response, next: NextFunction): void {
        try {
            const userId = new GetNotificationRequest(req).userId;
            this.setupSseConnection(userId, req, res, '/sse/notifications');
            const channel = this.redisService.getNotificationChannel(userId);
            (async () => {
                setInterval(() => { this.sendHeartbeat(); }, 10000);
                await this.redisService.subscribeToChannel(channel, this.sendNotificationToClients.bind(this));
            })();
        } catch (error) {

            console.log(error);

            next(error);
        }
    }

    public handleGetMessageRequest(req: Request, res: Response, next: NextFunction): void {
        try {
            const requestObj = new GetMessageRequest(req);
            this.setupSseConnection(requestObj.loginUserId, req, res, '/sse/message');
            const channel = this.redisService.getMessageChannel(requestObj.loginUserId, requestObj.recipientUserId);
            (async () => {
                setInterval(() => this.sendHeartbeat(), 10000);
                await this.redisService.subscribeToChannel(channel, this.sendMessageToClients.bind(this));
            })();
        } catch (error) {
            next(error);
        }
    }

    public handlePostNotificationRequest(req: Request, res: Response, next: NextFunction): void {
        try {
            const requestObj = new PostNotificationRequest(req);
            const notificationDTO = requestObj.notificationDTO;
            const loginUserId = notificationDTO.notifiedUserId;
            const channel = this.redisService.getNotificationChannel(loginUserId);
            const message = requestObj.notificationDTO.toString();
            (async () => {
                await this.redisService.publishToChannel(channel, message);
            })();
            res.sendStatus(200);
        } catch (error) {

            console.log(error);

            next(error);
        }
    }

    public handlePostMessageRequest(req: Request, res: Response, next: NextFunction): void {
        try {
            const requestObj = new PostMessageRequest(req);
            const messageDTO = requestObj.messageDTO;
            const channel = this.redisService.getMessageChannel(messageDTO.senderId, messageDTO.recipientId);
            const message = messageDTO.toString();
            (async () => {
                await this.redisService.publishToChannel(channel, message);
            })();
            res.sendStatus(200);
        } catch (error) {
            next(error);
        }
    }

    private setupSseConnection(userId: number, req: Request, res: Response, route: string): void {
        const requestClient = new RequestClient(userId, req, res);
        this.requestClients.push(requestClient);
        res.setHeader('Content-Type', 'text/event-stream');
        res.setHeader('Cache-Control', 'no-cache');
        res.setHeader('Connection', 'keep-alive');
        res.setHeader('Access-Control-Allow-Origin', '*');
        res.setHeader('Access-Control-Allow-Credentials', 'true');
        res.flushHeaders();
        req.on('close', () => {
            this.logger.logInfo(`Client disconnected from ${route}: ${req.ip}`);
        });
    }

    public sendHeartbeat(): void {
        this.requestClients.forEach(client => {
            client.res.write(`: heartbeat\n\n`);
        });
    }

    public sendNotificationToClients(message: string): void {
        this.requestClients.forEach(client => {
            client.res.write(`data: ${message}\n\n`);
        });
    }

    public sendMessageToClients(message: string): void {
        this.requestClients.forEach(client => {
            client.res.write(`data: ${message}\n\n`);
        });
    }
}
