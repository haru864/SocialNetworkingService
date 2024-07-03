import { Request, Response } from "express";

export class RequestClient {

    public userId: number;
    public req: Request;
    public res: Response;

    constructor(userId: number, req: Request, res: Response) {
        this.userId = userId;
        this.req = req;
        this.res = res;
    }
}