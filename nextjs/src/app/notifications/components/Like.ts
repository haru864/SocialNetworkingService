export class Like {
    id: number;
    userId: number;
    tweetId: number;
    likeDatetime: string;

    constructor(data: any) {
        this.id = data['id'] as number;
        this.userId = data['userId'] as number;
        this.tweetId = data['tweetId'] as number;
        this.likeDatetime = data['likeDatetime'] as string;
    }
}
