import { createClient } from 'redis';
import { Logger } from '../utils/Logger';

export class RedisService {

    private redisHost = process.env.REDIS_SERVER_ADDRESS || 'localhost';
    private redisPort = process.env.REDIS_SERVER_PORT || 6379;
    private client;
    private logger;

    constructor() {
        this.logger = Logger.getInstance();
        this.client = createClient({
            url: `redis://${this.redisHost}:${this.redisPort}`
        })
            .on('error', err => {
                console.log('Redis Client Error', err)
                this.logger.logError('[Redis Client Error] ' + err);
            });
    }

    public async connect() {
        await this.client.connect()
            .then(() => {
                console.log(`Connected to Redis at ${this.redisHost}:${this.redisPort}`);
                this.logger.logError(`Connected to Redis at ${this.redisHost}:${this.redisPort}`);
            })
            .catch(err => {
                console.error('Could not connect to Redis:', err);
                this.logger.logError('Could not connect to Redis: ' + err);
            });
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
