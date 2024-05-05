import React, { useState, useEffect } from 'react';
import { useSearchParams } from 'next/navigation';
import InfiniteScroll from 'react-infinite-scroll-component';
import {
    Grid, Box, Typography, CircularProgress
} from '@mui/material';
import TweetCard from '@/app/common/TweetCard';
import { getReplyTweetIds } from './ReplyListFunctions';

// BUG リロードしないとリプライが表示されない場合がある
const ReplyList: React.FC = () => {
    const [isLoading, setIsLoading] = useState<boolean>(true);
    const [isValidQuery, setIsValidQuery] = useState<boolean>(true);
    const [currentTweetId, setCurrentTweetId] = useState<number | null>(null);

    const [replyIds, setReplyIds] = useState<number[]>([]);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [page, setPage] = useState<number>(1);
    const searchParams = useSearchParams();

    useEffect(() => {
        const idString = searchParams.get('id');
        const idNumber = idString ? Number(idString) : null;
        if (idNumber === null || isNaN(idNumber)) {
            setIsValidQuery(false);
        } else {
            setCurrentTweetId(idNumber);
        }
    }, [searchParams]);

    useEffect(() => {
        if (currentTweetId !== null) {
            loadTweets();
        }
    }, [currentTweetId]);

    const loadTweets = async () => {
        setIsLoading(true);
        setReplyIds([]);
        setPage(1);
        setHasMore(true);
        await loadMoreReplies();
        setIsLoading(false);
    };

    const loadMoreReplies = async () => {
        if (currentTweetId === null) {
            return;
        }
        const currentReplies = await getReplyTweetIds(currentTweetId, page);
        setReplyIds(prev => [...prev, ...currentReplies]);
        setPage(page + 1);
        setHasMore(currentReplies.length === 20);
    };

    if (isLoading) {
        return (
            <Box display="flex" justifyContent="center" alignItems="center" height="100vh">
                <CircularProgress />
            </Box>
        );
    } else if (!isValidQuery || currentTweetId === null) {
        return (
            <Box sx={{ maxWidth: 600, margin: 'auto', textAlign: 'center', mt: 10 }}>
                <Typography variant="h4" gutterBottom>
                    Sorry, tweet not found.
                </Typography>
                <Typography variant="body1" sx={{ mb: 4 }}>
                    Please make sure that the URL you entered is correct.
                </Typography>
            </Box>
        );
    } else {
        return (
            <InfiniteScroll
                dataLength={replyIds.length}
                next={loadMoreReplies}
                hasMore={hasMore}
                loader={<h4>Loading...</h4>}
                endMessage={
                    <p style={{ textAlign: 'center', marginTop: '20px' }}>
                        <b>You have seen all tweets</b>
                    </p>
                }
            >
                <Grid container spacing={2} sx={{ marginBottom: '10px' }}>
                    <TweetCard key={currentTweetId} tweetId={currentTweetId} />
                </Grid>
                <Grid container spacing={2} sx={{ marginLeft: '100px' }}>
                    {replyIds.map(replyId => (
                        <TweetCard key={replyId} tweetId={replyId} />
                    ))}
                </Grid>
            </InfiniteScroll>
        );
    }
};

export default ReplyList;
