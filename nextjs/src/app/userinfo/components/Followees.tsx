import React, { useState, useEffect } from 'react';
import { useSearchParams } from 'next/navigation';
import InfiniteScroll from 'react-infinite-scroll-component';
import { Grid } from '@mui/material';
import { UserInfo } from '../../common/UserInfo';
import UserInfoCard from '@/app/common/UserInfoCard';

async function getFolloweeIds(userId: number, page: number): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/follows/followee?user_id=${userId}&page=${page}&limit=20`, {
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

async function getFollweeInfoList(userId: number, page: number): Promise<UserInfo[]> {
    try {
        let followerInfoList: UserInfo[] = [];
        const follwerIds = await getFolloweeIds(userId, page);
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

const Followees: React.FC = () => {
    const [followeeInfoList, setFolloweeInfoList] = useState<UserInfo[]>([]);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [page, setPage] = useState<number>(1);
    const searchParams = useSearchParams();
    const query = searchParams.get('id') as string;

    useEffect(() => {
        if (query) {
            const parsedId = parseInt(query, 10);
            if (!isNaN(parsedId)) {
                refreshFollowers(parsedId, page);
            }
        }
    }, [query]);

    const refreshFollowers = async (id: number, page: number) => {
        setFolloweeInfoList([]);
        setPage(1);
        setHasMore(true);
        await loadMoreFollowers(id, page);
    };

    const loadMoreFollowers = async (id: number, page: number) => {
        const currentFollowerList = await getFollweeInfoList(id, page);
        setFolloweeInfoList(prev => [...prev, ...currentFollowerList]);
        setPage(page + 1);
        setHasMore(currentFollowerList.length === 20);
    };

    return (
        <InfiniteScroll
            dataLength={followeeInfoList.length}
            next={() => loadMoreFollowers(parseInt(query), page)}
            hasMore={hasMore}
            loader={<h4>Loading...</h4>}
            endMessage={
                <p style={{ textAlign: 'center', marginTop: '20px' }}>
                    <b>You have seen all followees</b>
                </p>
            }
        >
            <Grid container spacing={2}>
                {followeeInfoList.map(followeeInfo => (
                    <UserInfoCard key={followeeInfo.username} userInfo={followeeInfo} />
                ))}
            </Grid>
        </InfiniteScroll>
    );
};

export default Followees;
