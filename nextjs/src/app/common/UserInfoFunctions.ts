import { UserInfo } from "./UserInfo";

export async function getUserinfo(userId: number | null = null): Promise<UserInfo> {
    try {
        let response: Response;
        if (userId === null) {
            response = await fetch(`${process.env.API_DOMAIN}/api/profile`, {
                method: 'GET',
                credentials: 'include'
            });
        } else {
            response = await fetch(`${process.env.API_DOMAIN}/api/profile?id=${userId}`, {
                method: 'GET',
                credentials: 'include'
            });
        }
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        if (jsonData === null) {
            throw new Error("Invalid response from server.");
        }
        const profile = jsonData['profile'];
        const userInfo = new UserInfo(profile);
        return userInfo;
    } catch (error: any) {
        console.error(error);
        alert(error);
        throw error;
    }
}
