import React, { useState, useEffect } from 'react';
import InfiniteScroll from 'react-infinite-scroll-component';
import {
    Grid,
    Card,
    CardContent,
    Typography,
    CardMedia,
    CardActionArea,
} from '@mui/material';
import { UserInfo } from '../../common/UserInfo';
import Link from 'next/link';

async function getFollowerIds(): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/follows?relation=follower`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let followerIds = [];
        if (jsonData !== null) {
            followerIds = jsonData['user_id'];
        }
        return followerIds;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

async function getFollwerInfo(): Promise<UserInfo[]> {
    try {
        let userInfoList: UserInfo[] = [];
        const follwerIds = await getFollowerIds();
        for (const follwerId of follwerIds) {
            const response = await fetch(`${process.env.API_DOMAIN}/api/profile?id=${follwerId}`, {
                method: 'GET',
                credentials: 'include'
            });
            if (!response.ok) {
                const responseData = await response.json();
                throw new Error(responseData["error_message"]);
            }
            const jsonData = await response.json();
            if (jsonData !== null) {
                const profile = jsonData['profile'];
                const followerData = new UserInfo(profile);
                userInfoList.push(followerData);
            }
        }
        return userInfoList;
    } catch (error: any) {
        console.error(error);
        alert(error);
        return [];
    }
}

// BUG 無限スクロールになってない
const FollowerList: React.FC = () => {
    const [userInfoList, setUserInfoList] = useState<UserInfo[]>([]);
    const [hasMore, setHasMore] = useState<boolean>(true);
    useEffect(() => {
        loadMoreFollowers();
    }, []);
    const loadMoreFollowers = async () => {
        const followerData = await getFollwerInfo();
        setUserInfoList(prev => [...prev, ...followerData]);
        setHasMore(followerData.length === 20);
    };
    return (
        <InfiniteScroll
            dataLength={setUserInfoList.length}
            next={loadMoreFollowers}
            hasMore={hasMore}
            loader={<h4>Loading...</h4>}
            endMessage={
                <p style={{ textAlign: 'center' }}>
                    <b>You have seen all followers</b>
                </p>
            }
        >
            <Grid container spacing={2}>
                {userInfoList.map(userInfo => (
                    <Grid item key={userInfo.username} xs={12} sm={6} md={4}>
                        <Link href={`/userinfo?id=${userInfo.id}`}>
                            <Card sx={{ maxWidth: 450 }}>
                                <CardActionArea>
                                    <Link href={userInfo.getUploadedImageUrl()}>
                                        <CardMedia
                                            component="img"
                                            image={userInfo.getThumbnailUrl()}
                                            alt="profile image"
                                            sx={{
                                                height: 140,
                                                width: 1,
                                                objectFit: 'contain'
                                            }}
                                        />
                                    </Link>
                                    <CardContent>
                                        <Typography gutterBottom variant="h5" component="div">
                                            {userInfo.username}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            {userInfo.selfIntroduction}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            Country: {userInfo.country}, State: {userInfo.state}, City: {userInfo.city}, Town: {userInfo.town}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            Hobbies: {userInfo.hobby_1}, {userInfo.hobby_2}, {userInfo.hobby_3}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            Careers: {userInfo.career_1}, {userInfo.career_2}, {userInfo.career_3}
                                        </Typography>
                                    </CardContent>
                                </CardActionArea>
                            </Card>
                        </Link>
                    </Grid>
                ))}
            </Grid>
        </InfiniteScroll>
    );
};

export default FollowerList;
