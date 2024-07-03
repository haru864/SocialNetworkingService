export async function getReplyTweetIds(tweetId: number, page: number): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets/${tweetId}/replies?page=${page}&limit=20`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let replyIds = [];
        if (jsonData !== null) {
            const repliesDataList = jsonData['replies'];
            for (const replyData of repliesDataList) {
                replyIds.push(replyData['id']);
            }
        }
        return replyIds;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}
