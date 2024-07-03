export class UserInfo {
    id: number;
    username: string;
    selfIntroduction: string;
    profileImage: string;
    country: string;
    state: string;
    city: string;
    town: string;
    hobby_1: string;
    hobby_2: string;
    hobby_3: string;
    career_1: string;
    career_2: string;
    career_3: string;
    isFollowedBy: boolean;
    isFollowing: boolean;

    constructor(data: any) {
        this.id = data['id'] as number;
        this.username = data['name'] as string;
        this.selfIntroduction = data['self_introduction'] as string;
        this.profileImage = data['profile_image'] as string;
        this.country = data['address']['country'] as string;
        this.state = data['address']['state'] as string;
        this.city = data['address']['city'] as string;
        this.town = data['address']['town'] as string;
        this.hobby_1 = data['hobbies'].length >= 1 ? data['hobbies'][0] : '';
        this.hobby_2 = data['hobbies'].length >= 2 ? data['hobbies'][1] : '';
        this.hobby_3 = data['hobbies'].length >= 3 ? data['hobbies'][2] : '';
        this.career_1 = data['careers'].length >= 1 ? data['careers'][0] : '';
        this.career_2 = data['careers'].length >= 2 ? data['careers'][1] : '';
        this.career_3 = data['careers'].length >= 3 ? data['careers'][2] : '';
        this.isFollowedBy = data['isFollowedBy'] as boolean;
        this.isFollowing = data['isFollowing'] as boolean;
    }

    getThumbnailUrl(): string {
        return `${process.env.PROFILE_IMAGE_THUMBNAIL_URL}/${this.profileImage}`;

    }

    getUploadedImageUrl(): string {
        return `${process.env.PROFILE_IMAGE_UPLOAD_URL}/${this.profileImage}`;
    }
}
