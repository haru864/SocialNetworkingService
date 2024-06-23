import { Request } from 'express';
import { MessageDTO } from '../../models/MessageDTO';

export class PostMessageRequest implements HttpRequestInterface {

    public userId: number;
    public messageDTO: MessageDTO;

    constructor(request: Request) {
        if (request.method !== `POST`) {
            throw new Error('Invalid Request Method');
        }
        if (request.body === null) {
            throw new Error('Request Body must be set');
        }
        const messageBody: { [key: string]: any } = request.body;
        this.userId = messageBody.user_id;
        this.messageDTO = new MessageDTO(
            messageBody.message.id,
            messageBody.message.senderId,
            messageBody.message.recipientId,
            messageBody.message.message,
            messageBody.message.mediaFileName,
            messageBody.message.mediaType,
            messageBody.message.sendDatetime
        );
    }
}