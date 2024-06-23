export class MessageDTO {

    public id: number;
    public senderId: number;
    public recipientId: number;
    public message: string;
    public mediaFileName: string;
    public mediaType: string;
    public sendDatetime: string;

    constructor(
        id: number,
        senderId: number,
        recipientId: number,
        message: string,
        mediaFileName: string,
        mediaType: string,
        sendDatetime: string
    ) {
        this.id = id;
        this.senderId = senderId;
        this.recipientId = recipientId;
        this.message = message;
        this.mediaFileName = mediaFileName;
        this.mediaType = mediaType;
        this.sendDatetime = sendDatetime;
    }

    public toString(): string {
        return JSON.stringify(this);
    }
}