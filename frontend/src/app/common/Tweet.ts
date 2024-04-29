export class Tweet {
    id: number;
    replyToId: number | null;
    userId: number;
    message: string;
    mediaFileName: string | null;
    mediaType: string | null;
    postingDatetime: string;

    constructor(data: any) {
        this.id = data['id'] as number;
        this.replyToId = data['reply_to_id'] as number | null;
        this.userId = data['userId'] as number;
        this.message = data['message'] as string;
        this.mediaFileName = data['mediaFileName'] as string | null;
        this.mediaType = data['mediaType'] as string | null;
        this.postingDatetime = data['postingDatetime'] as string;
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
}
