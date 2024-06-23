import { Request } from 'express';
import { NotificationDTO } from '../../models/NotificationDTO';

export class PostNotificationRequest implements HttpRequestInterface {

    public notificationDTO: NotificationDTO;

    constructor(request: Request) {
        if (request.method !== `POST`) {
            throw new Error('Invalid Request Method');
        }
        if (request.body === null) {
            throw new Error('Request Body must be set');
        }
        const messageBody: { [key: string]: any } = request.body;
        this.notificationDTO = new NotificationDTO(
            messageBody.notification.notificationType,
            messageBody.notification.id,
            messageBody.notification.notifiedUserId,
            messageBody.notification.entityId,
            messageBody.notification.isConfirmed,
            messageBody.notification.createdAt
        );
    }
}