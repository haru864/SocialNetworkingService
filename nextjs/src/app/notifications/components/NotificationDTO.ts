export class NotificationDTO {
    notificationType: string;
    id: number;
    notifiedUserId: number;
    entityId: number;
    isConfirmed: boolean;
    createdAt: string;
    
    constructor(data: any) {
        this.notificationType = data['notificationType'] as string;
        this.id = data['id'] as number;
        this.notifiedUserId = data['notifiedUserId'] as number;
        this.entityId = data['entityId'] as number;
        this.isConfirmed = data['isConfirmed'] as boolean;
        this.createdAt = data['createdAt'] as string;
    }
}
