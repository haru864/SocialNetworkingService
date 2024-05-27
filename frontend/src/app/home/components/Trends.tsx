import React, { useState, useEffect } from 'react';
import InfiniteScroll from 'react-infinite-scroll-component';
import { Grid } from '@mui/material';
import TweetCard from '@/app/common/TweetCard';

async function getTrendTweetIds(page: number): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets?type=trend&page=${page}&limit=20`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let tweetIds = [];
        if (jsonData !== null) {
            const tweetDataList = jsonData['tweets'];
            for (const tweetData of tweetDataList) {
                tweetIds.push(tweetData['id']);
            }
        }
        return tweetIds;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

const Trends = () => {
    const [tweetIds, setTweetIds] = useState<number[]>([]);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [page, setPage] = useState<number>(1);

    useEffect(() => {
        refreshTweets();
    }, []);

    const refreshTweets = async () => {
        setTweetIds([]);
        setPage(1);
        setHasMore(true);
        await loadMoreTweets();
    };

    const loadMoreTweets = async () => {
        const currentTweetIds = await getTrendTweetIds(page);
        setTweetIds(prev => [...prev, ...currentTweetIds]);
        setPage(page + 1);
        setHasMore(currentTweetIds.length > 0);
    };

    return (
        <InfiniteScroll
            dataLength={tweetIds.length}
            next={() => loadMoreTweets()}
            hasMore={hasMore}
            loader={<h4>Loading...</h4>}
            endMessage={
                <p style={{ textAlign: 'center', marginTop: '20px' }}>
                    <b>You have seen all tweets</b>
                </p>
            }
        >
            <Grid container spacing={2}>
                {tweetIds.map(tweetId => (
                    <TweetCard key={tweetId} tweetId={tweetId} />
                ))}
            </Grid>
        </InfiniteScroll>
    );
};

export default Trends;
