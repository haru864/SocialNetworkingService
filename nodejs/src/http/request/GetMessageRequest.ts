import { Request } from 'express';

export class GetMessageRequest implements HttpRequestInterface {

    public loginUserId: number;
    public recipientUserId: number;

    constructor(request: Request) {
        if (request.method !== `GET`) {
            throw new Error('Invalid Request Method');
        }
        const query: { [key: string]: any } = request.query;
        this.loginUserId = query.login_user_id;
        this.recipientUserId = query.recipient_user_id;
    }
}