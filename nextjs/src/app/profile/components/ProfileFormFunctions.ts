import { ProfileInfo } from './ProfileInfo';
import * as ValidationUtil from '../../utils/ValidationUtil';

export async function getUserData(): Promise<ProfileInfo | null> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/profile`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let profile = null;
        if (jsonData !== null) {
            profile = jsonData['profile'];
        }
        return new ProfileInfo(profile);
    } catch (error: any) {
        console.error(error);
        alert(error);
        return null;
    }
}

export async function updateProfile(
    event: React.FormEvent<HTMLFormElement>,
    setLoading: React.Dispatch<React.SetStateAction<boolean>>
): Promise<void> {
    try {
        event.preventDefault();
        const data: FormData = new FormData(event.currentTarget);
        data.append("action", "edit");
        const username: string = data.get('username') as string;
        const password: string = data.get('password') as string;
        const passwordConf: string = data.get('password_confirmation') as string;
        const email: string = data.get('email') as string;
        const selfIntro: string = data.get('self_introduction') as string;
        const country: string = data.get('country') as string;
        const state: string = data.get('state') as string;
        const city: string = data.get('city') as string;
        const town: string = data.get('town') as string;
        const hobby_1: string = data.get('hobby_1') as string;
        const hobby_2: string = data.get('hobby_2') as string;
        const hobby_3: string = data.get('hobby_3') as string;
        const career_1: string = data.get('career_1') as string;
        const career_2: string = data.get('career_2') as string;
        const career_3: string = data.get('career_3') as string;
        ValidationUtil.validateRequiredFields(username, "Username");
        ValidationUtil.validateRequiredFields(password, "Password");
        ValidationUtil.validateRequiredFields(password, "passwordConf");
        ValidationUtil.validateRequiredFields(password, "email");
        ValidationUtil.validateUsername(username);
        if (password !== passwordConf) {
            throw new Error('Confirmation password does not match the other.');
        }
        ValidationUtil.validatePassword(password);
        ValidationUtil.validateEmail(email);
        ValidationUtil.validateCharCount(email, "Email", null, 100);
        ValidationUtil.validateCharCount(selfIntro, "Self Introduction", null, 50);
        ValidationUtil.validateCharCount(country, "Country", null, 100);
        ValidationUtil.validateCharCount(state, "State", null, 100);
        ValidationUtil.validateCharCount(city, "City", null, 100);
        ValidationUtil.validateCharCount(town, "Town", null, 100);
        ValidationUtil.validateCharCount(hobby_1, "Hobby_1", null, 100);
        ValidationUtil.validateCharCount(hobby_2, "Hobby_2", null, 100);
        ValidationUtil.validateCharCount(hobby_3, "Hobby_3", null, 100);
        ValidationUtil.validateCharCount(career_1, "Career_1", null, 100);
        ValidationUtil.validateCharCount(career_2, "Career_2", null, 100);
        ValidationUtil.validateCharCount(career_3, "Career_3", null, 100);
        setLoading(true);
        const response = await fetch(`${process.env.API_DOMAIN}/api/profile`, {
            method: 'POST',
            body: data
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        alert('URL for authentication has been sent to the email address you entered.');
    } catch (error: any) {
        console.error(error);
        alert(error);
    } finally {
        setLoading(false);
    }
}

export async function deleteAccount(): Promise<void> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/profile`, {
            method: 'DELETE',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        return;
    } catch (error: any) {
        console.error(error);
        alert(error);
        return;
    }
}
