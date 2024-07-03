import React, { useState, useEffect } from 'react';
import InfiniteScroll from 'react-infinite-scroll-component';
import { Grid } from '@mui/material';
import { UserInfo } from '../../common/UserInfo';
import UserInfoCard from '@/app/common/UserInfoCard';

async function getUserIds(query: string, field: string, page: number): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/search/users?query=${query}&field=${field}&page=${page}&limit=20`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const respJson = await response.json();
        let userDatas = respJson['users'];

        let userIds = [];
        for (const userData of userDatas) {
            userIds.push(userData['id']);
        }
        return userIds;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

async function getUserInfoList(query: string, field: string, page: number): Promise<UserInfo[]> {
    try {
        const userIds = await getUserIds(query, field, page);
        let userInfos: UserInfo[] = [];
        for (const userId of userIds) {
            const response = await fetch(`${process.env.API_DOMAIN}/api/profile?id=${userId}`, {
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
                const currentUserInfo = new UserInfo(profile);
                userInfos.push(currentUserInfo);
            }
        }
        return userInfos;
    } catch (error: any) {
        console.error(error);
        alert(error);
        return [];
    }
}

interface prop {
    field: string;
}

const UserProfiles: React.FC<prop> = ({ field }) => {
    const [userInfoList, setUserInfoList] = useState<UserInfo[]>([]);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [page, setPage] = useState<number>(1);
    const [query, setQuery] = useState<string>('');

    useEffect(() => {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const currentQuery = urlParams.get('query');
        if (currentQuery !== null) {
            setQuery(currentQuery);
        }
    }, []);

    useEffect(() => {
        if (query === '') {
            return;
        }
        refreshUserProfiles();
    }, [query]);

    const refreshUserProfiles = async () => {
        setUserInfoList([]);
        setPage(1);
        setHasMore(true);
        await loadMoreUserProfiles();
    };

    const loadMoreUserProfiles = async () => {
        const currentUserInfoList = await getUserInfoList(query, field, page);
        setUserInfoList(prev => [...prev, ...currentUserInfoList]);
        setPage(page + 1);
        setHasMore(currentUserInfoList.length === 20);
    };

    return (
        <InfiniteScroll
            dataLength={userInfoList.length}
            next={loadMoreUserProfiles}
            hasMore={hasMore}
            loader={<h4>Loading...</h4>}
            endMessage={
                <p style={{ textAlign: 'center', marginTop: '20px' }}>
                    <b>You have seen all users</b>
                </p>
            }
        >
            <Grid container spacing={2}>
                {userInfoList.map(userInfo => (
                    <UserInfoCard key={userInfo.username} userInfo={userInfo} />
                ))}
            </Grid>
        </InfiniteScroll>
    );
};

export default UserProfiles;
