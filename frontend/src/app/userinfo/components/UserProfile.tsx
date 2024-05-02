import { useState, useEffect } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import { UserInfo } from '../../common/UserInfo';
import { Box, Typography, CircularProgress, Button } from '@mui/material';
import Link from 'next/link';
import ContentArea from './ContentArea';

async function getUserinfo(userId: number): Promise<UserInfo> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/profile?id=${userId}`, {
            method: 'GET',
            credentials: 'include'
        });
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

async function followUser(
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

async function unfollowUser(
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

const UserProfile: React.FC = () => {
    const [userInfo, setUserInfo] = useState<UserInfo | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const searchParams = useSearchParams();
    const query = searchParams.get('id');
    useEffect(() => {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const idString = urlParams.get('id');
        if (idString !== null) {
            const parsedId = parseInt(idString, 10);
            loadUserInfo(parsedId);
        }
    }, [query]);
    const loadUserInfo = async (id: number) => {
        const userInfo = await getUserinfo(id);
        setUserInfo(userInfo);
        setIsLoading(false);
    };
    const handleFollow = async () => {
        if (userInfo === null) {
            return;
        }
        await followUser(userInfo.id, setIsLoading);
        loadUserInfo(userInfo.id);
    };
    const handleUnfollow = async () => {
        if (userInfo === null) {
            return;
        }
        await unfollowUser(userInfo.id, setIsLoading);
        loadUserInfo(userInfo.id);
    };
    if (isLoading) {
        return (
            <Box display="flex" justifyContent="center" alignItems="center" height="100vh">
                <CircularProgress />
            </Box>
        );
    }
    if (userInfo === null) {
        return (
            <Box sx={{ maxWidth: 600, margin: 'auto', textAlign: 'center', mt: 10 }}>
                <Typography variant="h4" gutterBottom>
                    Sorry, user not found.
                </Typography>
                <Typography variant="body1" sx={{ mb: 4 }}>
                    Please make sure that the URL you entered is correct. Alternatively, you can return to the top page by clicking the link below.
                </Typography>
                <Button variant="contained" color="primary">
                    <Link href="/home">
                        <Typography component="a">HOME</Typography>
                    </Link>
                </Button>
            </Box>
        );
    } else {
        return (
            <Box sx={{ width: '100%' }}>
                <Typography variant="h4">{userInfo.username}</Typography>
                <Link href={userInfo.getUploadedImageUrl()}>
                    <img src={userInfo.getThumbnailUrl()} alt={`${userInfo.username}'s profile`} />
                </Link>
                <Typography variant="body1">{userInfo.selfIntroduction}</Typography>
                <Typography variant="body2" color="text.secondary">
                    Country: {userInfo.country}, State: {userInfo.state}, City: {userInfo.city}, Town: {userInfo.town}
                </Typography>
                <Typography variant="body2" color="text.secondary">
                    Hobbies: {userInfo.hobby_1}, {userInfo.hobby_2}, {userInfo.hobby_3}
                </Typography>
                <Typography variant="body2" color="text.secondary">
                    Careers: {userInfo.career_1}, {userInfo.career_2}, {userInfo.career_3}
                </Typography>
                {userInfo.isFollowedBy && (
                    <Typography variant="body2" color="secondary">
                        You are followed.
                    </Typography>
                )}
                {userInfo.isFollowing ? (
                    <>
                        <Typography variant="body2">You are following.</Typography>
                        <Button variant="contained" color="secondary" onClick={handleUnfollow}>
                            Unfollow
                        </Button>
                    </>
                ) : (
                    <Button variant="contained" color="primary" onClick={handleFollow}>
                        Follow
                    </Button>
                )}
                <ContentArea />
            </Box>
        );
    }
};

export default UserProfile;
