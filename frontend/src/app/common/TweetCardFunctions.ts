import { Tweet } from '@/app/common/Tweet';
import * as ValidationUtil from "../utils/ValidationUtil";

export const getTweet = async (tweetId: number): Promise<Tweet> => {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets/${tweetId}`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        if (jsonData === null) {
            throw new Error('Invalid server response.');
        }
        const tweetData = jsonData['tweet'];
        tweetData['likeUserIds'] = await getLikeUserIds(tweetData['id']);
        tweetData['retweetUserIds'] = await getRetweetUserIds(tweetData['id']);
        tweetData['replyUserIds'] = await getReplyUserIds(tweetData['id']);
        const tweet = new Tweet(tweetData);
        return tweet;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

async function getLikeUserIds(tweetId: number): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/likes?tweet_id=${tweetId}`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let likeUserIds = [];
        if (jsonData !== null) {
            likeUserIds = jsonData['user_id'];
        }
        return likeUserIds;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

async function getRetweetUserIds(tweetId: number): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets/${tweetId}/retweets`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let retweetUserIds = [];
        if (jsonData !== null) {
            const retweets = jsonData['retweets'];
            for (const retweet of retweets) {
                retweetUserIds.push(retweet['userId']);
            }
        }
        return retweetUserIds;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

async function getReplyUserIds(tweetId: number): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets/${tweetId}/replies`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let replyUserIds = [];
        if (jsonData !== null) {
            const replies = jsonData['replies'];
            for (const reply of replies) {
                replyUserIds.push(reply['userId']);
            }
        }
        return replyUserIds;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

export const handleLike = async (tweetId: number): Promise<void> => {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/likes?tweet_id=${tweetId}`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        if (jsonData === null) {
            throw new Error("Invalid Response from server.");
        }
        const isLiked = jsonData['is_liked'];
        if (isLiked) {
            await removeLike(tweetId);
        } else {
            await addLike(tweetId);
        }
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

async function addLike(tweetId: number): Promise<void> {
    try {
        const msgBody = {
            action: 'add',
            tweet_id: tweetId
        };
        const response = await fetch(`${process.env.API_DOMAIN}/api/likes`, {
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
        throw error;
    }
}

async function removeLike(tweetId: number): Promise<void> {
    try {
        const msgBody = {
            action: 'remove',
            tweet_id: tweetId
        };
        const response = await fetch(`${process.env.API_DOMAIN}/api/likes`, {
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
        throw error;
    }
}

export const checkRetweetedOrNot = async (tweetId: number): Promise<boolean> => {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets/${tweetId}/retweets`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        const isRetweeted = jsonData['is_retweeted'];
        return isRetweeted
    } catch (error: any) {
        console.error(error);
        alert(error);
        throw error;
    }
}

export const addRetweet = async (tweetId: number, retweetMsg: string): Promise<void> => {
    try {
        const trimmedMessage = retweetMsg.trim();
        if (trimmedMessage.length > 200) {
            alert('Invalid message length. Message must be up to 200 characters.');
            return;
        }
        const msgBody = {
            message: retweetMsg
        };
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets/${tweetId}/retweets`, {
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
        throw error;
    }
}

export const removeRetweet = async (tweetId: number): Promise<void> => {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets/${tweetId}/retweets`, {
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
        throw error;
    }
}

export const addReply = async (tweetId: number, replyText: string, replyFile: File | null): Promise<void> => {
    try {
        ValidationUtil.validateCharCount(replyText, "Message", 1, 200);
        const data: FormData = new FormData();
        data.append("message", replyText);
        if (replyFile !== null) {
            data.append("media", replyFile);
        }
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets/${tweetId}/replies`, {
            method: 'POST',
            credentials: 'include',
            body: data
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        alert('Reply posted.');
    } catch (error: any) {
        console.error(error);
        alert(error);
    }
}

export const deleteTweet = async (tweetId: number): Promise<void> => {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets/${tweetId}`, {
            method: 'DELETE',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
    } catch (error: any) {
        console.error(error);
        alert(error);
    }
}

// TODO リツイートもツイート一覧に表示できるようにしたらdeleteTweet()と使い分けが必要（そもそも統一すべきかも）
export const deleteRetweet = async (tweetId: number): Promise<void> => {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets/${tweetId}/retweets`, {
            method: 'DELETE',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
    } catch (error: any) {
        console.error(error);
        alert(error);
    }
}
