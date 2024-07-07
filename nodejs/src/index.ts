import express from 'express';
import dotenv from 'dotenv';
import sseRoutes from './routes/sseRoutes';
import { RedisService } from './services/RedisService';
import { errorHandler } from './middlewares/errorHandler';
import { loggerMiddleware } from './middlewares/loggerMiddleware';
import { Logger } from './utils/Logger';

console.log('Starting server...');

dotenv.config();
const PORT = process.env.NODE_JS_PORT || 3000;

const app = express();
const redisService = new RedisService();
const logger = Logger.getInstance();

app.use(express.json());
app.use(loggerMiddleware);
app.use(sseRoutes);
app.use(errorHandler)

const startServer = async () => {
  await redisService.connect();
  app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
  });
};

startServer().catch((err) => {
  logger.logError('Failed to start server:' + err);
});
