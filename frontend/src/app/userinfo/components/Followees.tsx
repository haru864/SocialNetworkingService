import React, { useState, useEffect } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
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

async function getFolloweeIds(userId: number): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/follows?id=${userId}&relation=followee`, {
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

async function getFollweeInfoList(userId: number): Promise<UserInfo[]> {
    try {
        let followeeInfoList: UserInfo[] = [];
        const follweeIds = await getFolloweeIds(userId);
        for (const follweeId of follweeIds) {
            const response = await fetch(`${process.env.API_DOMAIN}/api/profile?id=${follweeId}`, {
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
                const followeeData = new UserInfo(profile);
                followeeInfoList.push(followeeData);
            }
        }
        return followeeInfoList;
    } catch (error: any) {
        console.error(error);
        alert(error);
        return [];
    }
}

// BUG 無限スクロールになってない
const Followees: React.FC = () => {
    const [followeeInfoList, setFolloweeInfoList] = useState<UserInfo[]>([]);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const router = useRouter();
    const searchParams = useSearchParams();
    const query = searchParams.get('id') as string;

    useEffect(() => {
        if (query) {
            const parsedId = parseInt(query, 10);
            if (!isNaN(parsedId)) {
                refreshFollowees(parsedId);
            }
        }
    }, [query]);

    const refreshFollowees = async (id: number) => {
        setFolloweeInfoList([]);
        await loadMoreFollowees(id);
    };

    const loadMoreFollowees = async (id: number) => {
        const followeeList = await getFollweeInfoList(id);
        setFolloweeInfoList(followeeList);
        setHasMore(followeeList.length === 20);
    };

    return (
        <InfiniteScroll
            dataLength={followeeInfoList.length}
            next={() => loadMoreFollowees(parseInt(query))}
            hasMore={hasMore}
            loader={<h4>Loading...</h4>}
            endMessage={
                <p style={{ textAlign: 'center' }}>
                    <b>You have seen all followers</b>
                </p>
            }
        >
            <Grid container spacing={2}>
                {followeeInfoList.map(followeeInfo => (
                    <Grid item key={followeeInfo.username} xs={12} sm={6} md={4}>
                        <Link href={`/userinfo?id=${followeeInfo.id}`}>
                            <Card sx={{ maxWidth: 450 }}>
                                <CardActionArea>
                                    <Link href={followeeInfo.getUploadedImageUrl()}>
                                        <CardMedia
                                            component="img"
                                            image={followeeInfo.getThumbnailUrl()}
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
                                            {followeeInfo.username}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            {followeeInfo.selfIntroduction}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            Country: {followeeInfo.country}, State: {followeeInfo.state}, City: {followeeInfo.city}, Town: {followeeInfo.town}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            Hobbies: {followeeInfo.hobby_1}, {followeeInfo.hobby_2}, {followeeInfo.hobby_3}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            Careers: {followeeInfo.career_1}, {followeeInfo.career_2}, {followeeInfo.career_3}
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

export default Followees;
