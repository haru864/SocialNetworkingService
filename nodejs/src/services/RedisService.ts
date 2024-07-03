import { createClient } from 'redis';
import { Logger } from '../utils/Logger';

export class RedisService {

    private client;
    private logger;

    constructor() {
        this.client = createClient();
        this.logger = Logger.getInstance();
    }

    public async connect() {
        await this.client.connect();
    }

    public async publishToChannel(channel: string, message: string): Promise<void> {
        const subscriber = this.client.duplicate();
        subscriber.on('error', err => this.logger.logError(err));
        await subscriber.connect();
        await subscriber.publish(channel, message);
    }

    public async subscribeToChannel(channel: string, callback: (message: string) => void): Promise<void> {
        const subscriber = this.client.duplicate();
        subscriber.on('error', err => this.logger.logError(err));
        await subscriber.connect();
        await subscriber.subscribe(channel, (message) => {
            this.logger.logInfo(`Message received from ${channel} channel: ${message}`);
            callback(message);
        });
    }

    public getMessageChannel(loginUserId: number, messagePartnerUserId: number): string {
        const maxUserId = (loginUserId >= messagePartnerUserId ? loginUserId : messagePartnerUserId);
        const minUserId = (loginUserId < messagePartnerUserId ? loginUserId : messagePartnerUserId);
        return `chat:${minUserId}:${maxUserId}`;
    }

    public getNotificationChannel(notifiedUserId: number): string {
        return `notification:${notifiedUserId}`;
    }
}
