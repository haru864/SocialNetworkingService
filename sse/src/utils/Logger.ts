import fs from 'fs';
import path from 'path';
import { Request, Response } from 'express';

export class Logger {

  private static logger: Logger | null = null;
  private outputLogLevelValue: number;
  private logDir: string;
  private logLevel = {
    DEBUG: { label: 'DEBUG', value: 0 },
    INFO: { label: 'INFO', value: 1 },
    WARNING: { label: 'WARNING', value: 2 },
    ERROR: { label: 'ERROR', value: 3 },
  } as const;

  private constructor(logDir: string) {
    this.logDir = logDir;
    if (!fs.existsSync(this.logDir)) {
      fs.mkdirSync(this.logDir, { recursive: true });
    }
    this.outputLogLevelValue = Number(process.env.OUTPUT_LOG_LEVEL_VALUE) || this.logLevel.WARNING.value;
  }

  public static getInstance(): Logger {
    if (this.logger === null) {
      require('dotenv').config();
      this.logger = new Logger(process.env.LOG_DIR_ABS_PATH || './logs');
    }
    return this.logger;
  }

  public logRequest(request: Request): void {
    const { method, url, headers, query, body } = request;
    const logMessage = `
        Request received:
        Method: ${method}
        URL: ${url}
        Headers: ${JSON.stringify(headers, null, 2)}
        Query Parameters: ${JSON.stringify(query, null, 2)}
        Body: ${JSON.stringify(body, null, 2)}
    `;
    this.logInfo(logMessage);
  }

  public logResponse(response: Response): void {
    const { statusCode, statusMessage, getHeaders } = response;
    const logMessage = `
      Response sent:
      Status Code: ${statusCode}
      Status Message: ${statusMessage}
      Headers: ${JSON.stringify(getHeaders(), null, 2)}
    `;
    this.logInfo(logMessage);
  }

  public logDebug(message: string): void {
    if (this.outputLogLevelValue > this.logLevel.DEBUG.value) {
      return;
    }
    this.log(this.logLevel.DEBUG.label, message);
  }

  public logInfo(message: string): void {
    if (this.outputLogLevelValue > this.logLevel.INFO.value) {
      return;
    }
    this.log(this.logLevel.INFO.label, message);
  }

  public logWarn(message: string): void {
    if (this.outputLogLevelValue > this.logLevel.WARNING.value) {
      return;
    }
    this.log(this.logLevel.WARNING.label, message);
  }

  public logError(message: string): void {
    if (this.outputLogLevelValue > this.logLevel.ERROR.value) {
      return;
    }
    this.log(this.logLevel.ERROR.label, message);
  }

  private log(level: string, message: string): void {
    const logFileName = this.getLogFileName();
    const logMessage = `[${new Date().toISOString()}] ${level} ${message}\n`;
    const logFilePath = this.logDir + '/' + logFileName;
    fs.appendFileSync(logFilePath, logMessage, 'utf8');
  }

  private getLogFileName(): string {
    const date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return path.join(this.logDir, `log_${year}-${month}-${day}.log`);
  }
}
