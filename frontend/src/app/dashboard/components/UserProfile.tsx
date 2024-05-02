import { useState, useEffect } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import { UserInfo } from '../../common/UserInfo';
import { Box, Typography, CircularProgress, Button } from '@mui/material';
import Link from 'next/link';
import ContentArea from './ContentArea';

async function getUserinfo(): Promise<UserInfo> {
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

const UserProfile: React.FC = () => {
    const [userInfo, setUserInfo] = useState<UserInfo | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    useEffect(() => {
        loadUserInfo();
    }, []);
    const loadUserInfo = async () => {
        const userInfo = await getUserinfo();
        setUserInfo(userInfo);
        setIsLoading(false);
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
                <ContentArea />
            </Box>
        );
    }
};

export default UserProfile;
