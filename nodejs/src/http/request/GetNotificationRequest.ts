import { Request } from 'express';

export class GetNotificationRequest implements HttpRequestInterface {

    public userId: number;

    constructor(request: Request) {
        if (request.method !== `GET`) {
            throw new Error('Invalid Request Method');
        }
        const query: { [key: string]: any } = request.query;
        this.userId = query.user_id;
    }
}