import express from 'express';
import dotenv from 'dotenv';
import sseRoutes from './routes/sseRoutes';
import { RedisService } from './services/RedisService';
import { SseController } from './controllers/SseController';
import { Logger } from './utils/Logger';
import { errorHandler } from './middlewares/errorHandler';

dotenv.config();
const PORT = process.env.NODE_JS_PORT || 3000;

const app = express();
const redisService = new RedisService();
const sseController = new SseController(redisService);
const logger: Logger = Logger.getInstance();

app.use(express.json());
app.use(sseRoutes);
app.use(errorHandler)

const startServer = async () => {
  await redisService.connect();
  // await redisService.subscribeToChannel('notifications', sseController.sendMessageToClients.bind(sseController));
  // await redisService.subscribeToChannel('messages', sseController.sendMessageToClients.bind(sseController));
  // setInterval(() => sseController.sendHeartbeat(), 10000);

  app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
  });
};

startServer().catch((err) => {
  logger.logError('Failed to start server:' + err);
});
