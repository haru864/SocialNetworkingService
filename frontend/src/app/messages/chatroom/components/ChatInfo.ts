export class ChatUser {
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

    getThumbnailUrl(): string {
        return `${process.env.MESSAGE_IMAGE_THUMBNAIL_URL}/${this.mediaFileName}`;

    }

    getUploadedImageUrl(): string {
        return `${process.env.MESSAGE_IMAGE_UPLOAD_URL}/${this.mediaFileName}`;
    }

    getUploadedVideoUrl(): string {
        return `${process.env.MESSAGE_VIDEO_UPLOAD_URL}/${this.mediaFileName}`;
    }
}

export class ChatInfo {
    loginUser: ChatUser;
    chatPartner: ChatUser;
    messages: Message[];

    constructor(loginUser: ChatUser, chatPartner: ChatUser, messages: Message[]) {
        this.loginUser = loginUser;
        this.chatPartner = chatPartner;
        this.messages = messages;
    }
}
