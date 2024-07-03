export class NotificationDTO {

    public notificationType: string;
    public id: number;
    public notifiedUserId: number;
    public entityId: number;
    public isConfirmed: boolean;
    public createdAt: string;

    constructor(
        notificationType: string,
        id: number,
        notifiedUserId: number,
        entityId: number,
        isConfirmed: boolean,
        createdAt: string
    ) {
        this.notificationType = notificationType;
        this.id = id;
        this.notifiedUserId = notifiedUserId;
        this.entityId = entityId;
        this.isConfirmed = isConfirmed;
        this.createdAt = createdAt;
    }

    public toString(): string {
        return JSON.stringify(this);
    }
}