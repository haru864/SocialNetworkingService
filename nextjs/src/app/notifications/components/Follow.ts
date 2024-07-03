export class Follow {
    id: number;
    followerId: number;
    followeeId: number;
    followDatetime: string;

    constructor(data: any) {
        this.id = data['id'] as number;
        this.followerId = data['followerId'] as number;
        this.followeeId = data['followeeId'] as number;
        this.followDatetime = data['followDatetime'] as string;
    }
}
