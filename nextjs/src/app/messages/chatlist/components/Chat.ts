export class ChatPartner {
    id: number;
    name: string;

    constructor(data: any) {
        this.id = data['id'] as number;
        this.name = data['name'] as string;
    }
}

export class Message {
    id: number;
    senderId: number;
    recipientId: number;
    message: string;
    mediaFileName: string;
    mediaType: string;
    sendDatetime: string;

    constructor(data: any) {
        this.id = data['id'] as number;
        this.senderId = data['senderId'] as number;
        this.recipientId = data['recipientId'] as number;
        this.message = data['message'] as string;
        this.mediaFileName = data['mediaFileName'] as string;
        this.mediaType = data['mediaType'] as string;
        this.sendDatetime = data['sendDatetime'] as string;
    }
}

export class Chat {
    chatPartner: ChatPartner;
    latestMessage: Message;

    constructor(chatPartner: ChatPartner, latestMessage: Message) {
        this.chatPartner = chatPartner;
        this.latestMessage = latestMessage;
    }
}
