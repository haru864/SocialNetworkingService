import express from 'express';
import { createClient } from 'redis';
import { Request, Response } from 'express';

// 環境変数からポート番号を取得、デフォルトは3000
const PORT = process.env.PORT || 3000;

const app = express();
const redisClient = createClient();

redisClient.on('error', (err) => {
  console.error('Redis client error:', err);
});

let clients: { id: number, res: Response }[] = [];

app.use(express.json());

app.get('/sse/notifications', (req: Request, res: Response) => {
  res.setHeader('Content-Type', 'text/event-stream');
  res.setHeader('Cache-Control', 'no-cache');
  res.setHeader('Connection', 'keep-alive');
  res.flushHeaders(); // flush the headers to establish SSE with client

  const clientId = Date.now();

  clients.push({
    id: clientId,
    res
  });

  req.on('close', () => {
    console.log(`${clientId} Connection closed`);
    clients = clients.filter(client => client.id !== clientId);
  });
});

app.get('/sse/message', (req: Request, res: Response) => {
  res.setHeader('Content-Type', 'text/event-stream');
  res.setHeader('Cache-Control', 'no-cache');
  res.setHeader('Connection', 'keep-alive');
  res.flushHeaders(); // flush the headers to establish SSE with client

  const clientId = Date.now();

  clients.push({
    id: clientId,
    res
  });

  req.on('close', () => {
    console.log(`${clientId} Connection closed`);
    clients = clients.filter(client => client.id !== clientId);
  });
});

// Redis subscribe to a channel
const subscribeToRedis = () => {
  const subscriber = redisClient.duplicate();
  subscriber.connect();
  subscriber.subscribe('notifications', (message) => {
    console.log('Message received from notifications channel:', message);
    clients.forEach(client => client.res.write(`data: ${message}\n\n`));
  });
  subscriber.subscribe('messages', (message) => {
    console.log('Message received from messages channel:', message);
    clients.forEach(client => client.res.write(`data: ${message}\n\n`));
  });
};

// Heartbeat to keep connections alive
setInterval(() => {
  clients.forEach(client => client.res.write(`data: heartbeat\n\n`));
}, 30000); // send heartbeat every 30 seconds

app.listen(PORT, () => {
  console.log(`Server is running on http://localhost:${PORT}`);
  redisClient.connect().then(subscribeToRedis);
});
