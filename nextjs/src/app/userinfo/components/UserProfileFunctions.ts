export async function followUser(
    userId: number,
    setIsLoading: React.Dispatch<React.SetStateAction<boolean>>
): Promise<void> {
    try {
        const msgBody = {
            action: 'add',
            followee_id: userId
        };
        setIsLoading(true);
        const response = await fetch(`${process.env.API_DOMAIN}/api/follows/follower`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(msgBody),
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
    } catch (error: any) {
        console.error(error);
        alert(error);
    } finally {
        setIsLoading(false);
    }
}

export async function unfollowUser(
    userId: number,
    setIsLoading: React.Dispatch<React.SetStateAction<boolean>>
): Promise<void> {
    try {
        const msgBody = {
            action: 'remove',
            followee_id: userId
        };
        setIsLoading(true);
        const response = await fetch(`${process.env.API_DOMAIN}/api/follows/follower`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(msgBody),
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
    } catch (error: any) {
        console.error(error);
        alert(error);
    } finally {
        setIsLoading(false);
    }
}
