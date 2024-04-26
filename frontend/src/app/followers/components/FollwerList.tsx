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
import { FollowerData } from './FollowerData';
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

async function getFollwerInfo(): Promise<FollowerData[]> {
    try {
        let followerDataList: FollowerData[] = [];
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
                const followerData = new FollowerData(profile);
                followerDataList.push(followerData);
            }
        }
        return followerDataList;
    } catch (error: any) {
        console.error(error);
        alert(error);
        return [];
    }
}

const FollowerList: React.FC = () => {
    const [followerDataList, setFollowerDataList] = useState<FollowerData[]>([]);
    const [hasMore, setHasMore] = useState<boolean>(true);
    useEffect(() => {
        loadMoreFollowers();
    }, []);
    const loadMoreFollowers = async () => {
        const followerData = await getFollwerInfo();
        setFollowerDataList(prev => [...prev, ...followerData]);
        if (followerData.length === 0) {
            setHasMore(false);
        }
    };
    return (
        <InfiniteScroll
            dataLength={setFollowerDataList.length}
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
                {followerDataList.map(followerData => (
                    <Grid item key={followerData.username} xs={12} sm={6} md={4}>
                        <Link href={`/userinfo?username=${followerData.username}`}>
                            <Card sx={{ maxWidth: 450 }}>
                                <CardActionArea>
                                    <Link href={followerData.getUploadedImageUrl()}>
                                        <CardMedia
                                            component="img"
                                            image={followerData.getThumbnailUrl()}
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
                                            {followerData.username}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            {followerData.selfIntroduction}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            Country: {followerData.country}, State: {followerData.state}, City: {followerData.city}, Town: {followerData.town}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            Hobbies: {followerData.hobby_1}, {followerData.hobby_2}, {followerData.hobby_3}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            Careers: {followerData.career_1}, {followerData.career_2}, {followerData.career_3}
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
