import React, { useState, useEffect } from 'react';
import InfiniteScroll from 'react-infinite-scroll-component';
import { Grid, Card, CardContent, Typography } from '@mui/material';
import { FollowerData } from './FollowerData';

async function getFollowerIds(userId: number): Promise<number[]> {
    const response = await fetch(`${process.env.API_DOMAIN}/api/follows`, {
        method: 'GET',
        credentials: 'include'
    });
    if (!response.ok) {
        const responseData = await response.json();
        throw new Error(responseData["error_message"]);
    }
    const jsonData = await response.json();
    let profile = null;
    if (jsonData !== null) {
        profile = jsonData['profile'];
    }
    return new UserData(profile);
}

const FollowerList: React.FC = () => {
    const [followerDataList, setFollowerDataList] = useState<FollowerData[]>([]);
    const [hasMore, setHasMore] = useState<boolean>(true);
    useEffect(() => {
        fetchData();
    }, []);
    const fetchData = async () => {
        const response = await fetch('APIのURL');
        const data = await response.json();
        const newItems: FollowerData[] = data.items;
        setItems((prevItems) => [...prevItems, ...newItems]);
        if (newItems.length === 0) {
            setHasMore(false);
        }
    };
    return (
        <InfiniteScroll
            dataLength={items.length}
            next={fetchData}
            hasMore={hasMore}
            loader={<h4>Loading...</h4>}
            endMessage={
                <p style={{ textAlign: 'center' }}>
                    <b>You have seen it all</b>
                </p>
            }
        >
            <Grid container spacing={2}>
                {items.map((item) => (
                    <Grid item xs={12} sm={6} md={4} key={item.id}>
                        <Card>
                            <CardContent>
                                <Typography variant="h5" component="div">
                                    {item.title}
                                </Typography>
                                <Typography variant="body2" color="text.secondary">
                                    {item.content}
                                </Typography>
                            </CardContent>
                        </Card>
                    </Grid>
                ))}
            </Grid>
        </InfiniteScroll>
    );
};

export default FollowerList;
