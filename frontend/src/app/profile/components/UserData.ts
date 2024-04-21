export class UserData {
    username: string;
    password: string;
    email: string;
    selfIntroduction: string;
    profileImage: string;
    country: string;
    state: string;
    city: string;
    town: string;
    hobbies: string[];
    careers: string[];

    constructor(data: { [key: string]: string | string[] }) {
        this.username = data['username'] as string;
        this.password = '*********';
        this.email = data['email'] as string;
        this.selfIntroduction = data['selfIntroduction'] as string;
        this.profileImage = data['profileImage'] as string;
        this.country = data['country'] as string;
        this.state = data['state'] as string;
        this.city = data['city'] as string;
        this.town = data['town'] as string;
        this.hobbies = data['hobbies'] as string[];
        this.careers = data['careers'] as string[];
    }
}
