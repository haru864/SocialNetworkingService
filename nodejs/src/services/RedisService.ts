import { createClient } from 'redis';
import { Logger } from '../utils/Logger';

export class RedisService {

    // private redisHost = process.env.REDIS_SERVER_ADDRESS || 'localhost';
    private redisHost = process.env.REDIS_SERVER_ADDRESS || 'redis';
    private redisPort = process.env.REDIS_SERVER_PORT || 6379;
    private pubClient;
    private subClient;
    private logger;

    constructor() {
        this.logger = Logger.getInstance();
        this.pubClient = createClient({
            url: `redis://${this.redisHost}:${this.redisPort}`
        }).on('error', err => {
            console.log('Redis Pub Client Error', err);
            this.logger.logError('[Redis Pub Client Error] ' + err);
        });
        this.subClient = createClient({
            url: `redis://${this.redisHost}:${this.redisPort}`
        }).on('error', err => {
            console.log('Redis Sub Client Error', err);
            this.logger.logError('[Redis Sub Client Error] ' + err);
        });
    }

    public async connect() {
        try {
            await this.pubClient.connect();
            await this.subClient.connect();
            console.log(`Connected to Redis at ${this.redisHost}:${this.redisPort}`);
            this.logger.logInfo(`Connected to Redis at ${this.redisHost}:${this.redisPort}`);
        } catch (err) {
            console.error('Could not connect to Redis:', err);
            this.logger.logError('Could not connect to Redis: ' + err);
        }
    }

    public async publishToChannel(channel: string, message: string): Promise<void> {
        console.log('Starting publishToChannel()...');
        if (!this.pubClient.isOpen) {
            await this.pubClient.connect();
        }
        console.log('Publishing to channel:', channel);
        try {
            await this.pubClient.publish(channel, message);
            console.log('Publish successful');
        } catch (err) {
            console.error('Error in publishToChannel(): ', err);
            this.logger.logError('Error in publishToChannel(): ' + err);
        }
    }

    public async subscribeToChannel(channel: string, callback: (message: string) => void): Promise<void> {
        console.log('Starting subscribeToChannel()...');
        if (!this.subClient.isOpen) {
            await this.subClient.connect();
        }
        try {
            console.log('Subscribing to channel:', channel);
            // BUG Redisに接続しない、PUBが成功してもコールバックが実行されない
            await this.subClient.subscribe(channel, (message) => {
                console.log(`Message received from ${channel} channel: ${message}`);
                this.logger.logInfo(`Message received from ${channel} channel: ${message}`);
                callback(message);
            });
            console.log('Subscription successful');
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
