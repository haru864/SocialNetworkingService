import React, { useState } from 'react';
import InfiniteScroll from 'react-infinite-scroll-component';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Typography from '@mui/material/Typography';

const initialTweets = Array.from({ length: 20 }, (_, i) => ({
    id: i + 1,
    author: `User ${i + 1}`,
    content: `This is tweet number ${i + 1}`
}));

const Timeline = () => {
    const [tweets, setTweets] = useState(initialTweets);
    const [hasMore, setHasMore] = useState(true);

    const fetchMoreTweets = () => {
        if (tweets.length >= 100) {
            setHasMore(false);
            return;
        }
        const newTweets = Array.from({ length: 20 }, (_, i) => ({
            id: tweets.length + i + 1,
            author: `User ${tweets.length + i + 1}`,
            content: `This is tweet number ${tweets.length + i + 1}`
        }));
        setTweets(tweets.concat(newTweets));
    };

    return (
        <InfiniteScroll
            dataLength={tweets.length}
            next={fetchMoreTweets}
            hasMore={hasMore}
            loader={<Typography>Loading...</Typography>}
            endMessage={
                <Typography style={{ textAlign: 'center' }}>
                    <b>Yay! You have seen it all</b>
                </Typography>
            }
        >
            {tweets.map(tweet => (
                <Card key={tweet.id} style={{ margin: '20px 0' }}>
                    <CardContent>
                        <Typography variant="h6">{tweet.author}</Typography>
                        <Typography color="textSecondary">{tweet.content}</Typography>
                    </CardContent>
                </Card>
            ))}
        </InfiniteScroll>
    );
};

export default Timeline;
