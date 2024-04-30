export class Tweet {
    id: number;
    replyToId: number | null;
    userId: number;
    message: string;
    mediaFileName: string | null;
    mediaType: string | null;
    postingDatetime: string;
    likeUserIds: number[];
    retweetUserIds: number[];
    replyUserIds: number[];

    constructor(data: any) {
        this.id = data['id'] as number;
        this.replyToId = data['replyToId'] as number | null;
        this.userId = data['userId'] as number;
        this.message = data['message'] as string;
        this.mediaFileName = data['mediaFileName'] as string | null;
        this.mediaType = data['mediaType'] as string | null;
        this.postingDatetime = data['postingDatetime'] as string;
        this.likeUserIds = data['likeUserIds'] as number[];
        this.retweetUserIds = data['retweetUserIds'] as number[];
        this.replyUserIds = data['replyUserIds'] as number[];
    }

    getThumbnailUrl(): string {
        return `${process.env.TWEET_IMAGE_THUMBNAIL_URL}/${this.mediaFileName}`;

    }

    getUploadedImageUrl(): string {
        return `${process.env.TWEET_IMAGE_UPLOAD_URL}/${this.mediaFileName}`;
    }

    getUploadedVideoUrl(): string {
        return `${process.env.TWEET_VIDEO_UPLOAD_URL}/${this.mediaFileName}`;
    }

    getLikeCount(): number {
        return this.likeUserIds.length;
    }

    getRetweetCount(): number {
        return this.retweetUserIds.length;
    }

    getReplyCount(): number {
        return this.replyUserIds.length;
    }
}
