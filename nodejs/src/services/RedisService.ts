import { createClient } from 'redis';
import { Logger } from '../utils/Logger';

export class RedisService {

    // private redisHost = process.env.REDIS_SERVER_ADDRESS || 'localhost';
    private redisHost = process.env.REDIS_SERVER_ADDRESS || 'redis';
    private redisPort = process.env.REDIS_SERVER_PORT || 6379;
    private publisher;
    private subscriber;
    private logger;

    constructor() {
        this.logger = Logger.getInstance();
        this.publisher = createClient({
            url: `redis://${this.redisHost}:${this.redisPort}`
        }).on('error', err => {
            console.log('Redis Pub Client Error', err);
            this.logger.logError('[Redis Pub Client Error] ' + err);
        });
        this.subscriber = createClient({
            url: `redis://${this.redisHost}:${this.redisPort}`
        }).on('error', err => {
            console.log('Redis Sub Client Error', err);
            this.logger.logError('[Redis Sub Client Error] ' + err);
        });
    }

    public async connect() {
        try {
            await this.publisher.connect();
            await this.subscriber.connect();
            console.log(`Connected to Redis at ${this.redisHost}:${this.redisPort}`);
            this.logger.logInfo(`Connected to Redis at ${this.redisHost}:${this.redisPort}`);
        } catch (err) {
            console.error('Could not connect to Redis:', err);
            this.logger.logError('Could not connect to Redis: ' + err);
        }
    }

    public async publishToChannel(channel: string, message: string): Promise<void> {
        if (!this.publisher.isOpen) {
            await this.publisher.connect();
        }
        try {
            await this.publisher.publish(channel, message);
        } catch (err) {
            console.error('Error in publishToChannel(): ', err);
            this.logger.logError('Error in publishToChannel(): ' + err);
        }
    }

    public async subscribeToChannel(channel: string, callback: (message: string) => void): Promise<void> {
        if (!this.subscriber.isOpen) {
            await this.subscriber.connect();
        }
        try {
            await this.subscriber.subscribe(channel, (message) => {
                callback(message);
            });
        } catch (err) {
            console.error('Error in subscribeToChannel(): ', err);
            this.logger.logError('Error in subscribeToChannel(): ' + err);
        }
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
