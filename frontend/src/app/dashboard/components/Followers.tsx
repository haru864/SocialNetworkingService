import React, { useState, useEffect } from 'react';
import { useSearchParams } from 'next/navigation';
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

async function getFollowerIds(page: number): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/follows/follower?page=${page}&limit=20`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let followeeIds = [];
        if (jsonData !== null) {
            followeeIds = jsonData['user_id'];
        }
        return followeeIds;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

async function getFollwerInfoList(page: number): Promise<UserInfo[]> {
    try {
        let followerInfoList: UserInfo[] = [];
        const follwerIds = await getFollowerIds(page);
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
                followerInfoList.push(followerData);
            }
        }
        return followerInfoList;
    } catch (error: any) {
        console.error(error);
        alert(error);
        return [];
    }
}

const Followers: React.FC = () => {
    const [followerInfoList, setFollowerInfoList] = useState<UserInfo[]>([]);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [page, setPage] = useState<number>(1);

    useEffect(() => {
        refreshFollowers(page);
    }, []);

    const refreshFollowers = async (page: number) => {
        setFollowerInfoList([]);
        setPage(1);
        setHasMore(true);
        await loadMoreFollowers(page);
    };

    const loadMoreFollowers = async (page: number) => {
        const currentFollowerList = await getFollwerInfoList(page);
        setFollowerInfoList(prev => [...prev, ...currentFollowerList]);
        setPage(page + 1);
        setHasMore(currentFollowerList.length === 20);
    };

    return (
        <InfiniteScroll
            dataLength={followerInfoList.length}
            next={() => loadMoreFollowers(page)}
            hasMore={hasMore}
            loader={<h4>Loading...</h4>}
            endMessage={
                <p style={{ textAlign: 'center', marginTop: '20px' }}>
                    <b>You have seen all followers</b>
                </p>
            }
        >
            <Grid container spacing={2}>
                {followerInfoList.map(followerInfo => (
                    <Grid item key={followerInfo.username} xs={12} sm={6} md={4}>
                        <Link href={`/userinfo?id=${followerInfo.id}`}>
                            <Card sx={{ maxWidth: 450 }}>
                                <CardActionArea>
                                    <Link href={followerInfo.getUploadedImageUrl()}>
                                        <CardMedia
                                            component="img"
                                            image={followerInfo.getThumbnailUrl()}
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
                                            {followerInfo.username}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            {followerInfo.selfIntroduction}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            Country: {followerInfo.country}, State: {followerInfo.state}, City: {followerInfo.city}, Town: {followerInfo.town}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            Hobbies: {followerInfo.hobby_1}, {followerInfo.hobby_2}, {followerInfo.hobby_3}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            Careers: {followerInfo.career_1}, {followerInfo.career_2}, {followerInfo.career_3}
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

export default Followers;
