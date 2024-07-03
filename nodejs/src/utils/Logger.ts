import fs from 'fs';
import path from 'path';
import { Request, Response } from 'express';
import moment from 'moment-timezone';
import dotenv from 'dotenv';

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
  private maxFileSizeBytes: number;

  private constructor(logDir: string) {
    this.logDir = logDir;
    if (!fs.existsSync(this.logDir)) {
      fs.mkdirSync(this.logDir, { recursive: true });
    }
    require('dotenv').config();
    this.outputLogLevelValue = Number(process.env.OUTPUT_LOG_LEVEL_VALUE) || this.logLevel.WARNING.value;
    this.maxFileSizeBytes = Number(process.env.MAX_LOG_FILE_SIZE_BYTES) || 5 * 1024 * 1024; // Default to 5MB
  }

  public static getInstance(): Logger {
    if (this.logger === null) {
      dotenv.config();
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
    const logMessage = `
      Response sent:
      Status Code: ${response.statusCode}
      Status Message: ${response.statusMessage}
      Headers: ${JSON.stringify(response.getHeaders(), null, 2)}
    `;
    this.logInfo(logMessage);
  }

  public logDebug(message: string): void {
    if (this.outputLogLevelValue < this.logLevel.DEBUG.value) {
      return;
    }
    this.log(this.logLevel.DEBUG.label, message);
  }

  public logInfo(message: string): void {
    if (this.outputLogLevelValue < this.logLevel.INFO.value) {
      return;
    }
    this.log(this.logLevel.INFO.label, message);
  }

  public logWarn(message: string): void {
    if (this.outputLogLevelValue < this.logLevel.WARNING.value) {
      return;
    }
    this.log(this.logLevel.WARNING.label, message);
  }

  public logError(message: string): void {
    if (this.outputLogLevelValue < this.logLevel.ERROR.value) {
      return;
    }
    this.log(this.logLevel.ERROR.label, message);
  }

  private log(level: string, message: string): void {
    const logFileName = this.getLogFileName();
    const timezoneDate = this.getDateByTimezone();
    const logMessage = `[${timezoneDate.toISOString()}] ${level} ${message}\n`;
    try {
      fs.appendFileSync(logFileName, logMessage, 'utf8');
    } catch (error) {
      console.log(error)
    }
  }

  private getLogFileName(): string {
    const timezoneDate = this.getDateByTimezone();
    const year = timezoneDate.getFullYear();
    const month = String(timezoneDate.getMonth() + 1).padStart(2, '0');
    const day = String(timezoneDate.getDate()).padStart(2, '0');

    const baseFileName = `log_${year}-${month}-${day}`;
    let counter = 1;
    let logFileName = path.join(this.logDir, `${baseFileName}_${counter}.log`);

    while (fs.existsSync(logFileName) && fs.statSync(logFileName).size > this.maxFileSizeBytes) {
      logFileName = path.join(this.logDir, `${baseFileName}_${counter}.log`);
      counter++;
    }

    return logFileName;
  }

  private getDateByTimezone(): Date {
    const timezone = process.env.TIMEZONE || 'UTC';
    const timezoneMoment = moment().tz(timezone);
    const utcOffsetMinutes = timezoneMoment.utcOffset();
    const utcMilliseconds = timezoneMoment.valueOf();
    const timezoneDate = new Date(utcMilliseconds + (utcOffsetMinutes * 60 * 1000));
    return timezoneDate;
  }
}
