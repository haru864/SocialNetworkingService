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
    IconButton
} from '@mui/material';
import FavoriteBorderIcon from '@mui/icons-material/FavoriteBorder';
import ChatBubbleOutlineIcon from '@mui/icons-material/ChatBubbleOutline';
import RepeatIcon from '@mui/icons-material/Repeat';
import Link from 'next/link';
import { Tweet } from '@/app/common/Tweet';

async function getTweets(userId: number, page: number): Promise<Tweet[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets?type=user&user_id=${userId}&page=${page}&limit=20`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let tweets = [];
        if (jsonData !== null) {
            const tweetDataList = jsonData['tweets'];
            for (const tweetData of tweetDataList) {
                tweetData['likeUserIds'] = await getLikeUserIds(tweetData['id']);
                tweetData['retweetUserIds'] = await getRetweetUserIds(tweetData['id']);
                tweetData['replyUserIds'] = await getReplyUserIds(tweetData['id']);

                console.log(tweetData['likeUserIds']);
                console.log(tweetData['retweetUserIds']);
                console.log(tweetData['replyUserIds']);

                const tweet = new Tweet(tweetData);
                tweets.push(tweet);
            }
        }
        return tweets;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

async function getLikeUserIds(tweetId: number): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/likes?tweet_id=${tweetId}`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let likeUserIds = [];
        if (jsonData !== null) {
            likeUserIds = jsonData['user_id'];
        }
        return likeUserIds;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

async function getRetweetUserIds(tweetId: number): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets/${tweetId}/retweets`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let retweetUserIds = [];
        if (jsonData !== null) {
            const retweets = jsonData['retweets'];
            for (const retweet of retweets) {
                retweetUserIds.push(retweet['userId']);
            }
        }
        return retweetUserIds;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

async function getReplyUserIds(tweetId: number): Promise<number[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets/${tweetId}/replies`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let replyUserIds = [];
        if (jsonData !== null) {
            const replies = jsonData['replies'];
            for (const reply of replies) {
                replyUserIds.push(reply['userId']);
            }
        }
        return replyUserIds;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

const Tweets: React.FC = () => {
    const [tweets, setTweets] = useState<Tweet[]>([]);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [page, setPage] = useState<number>(1);
    const searchParams = useSearchParams();
    const query = searchParams.get('id') as string;

    useEffect(() => {
        if (query) {
            const parsedId = parseInt(query, 10);
            if (!isNaN(parsedId)) {
                refreshFollowers(parsedId);
            }
        }
    }, [query]);

    const refreshFollowers = async (id: number) => {
        setTweets([]);
        setPage(1);
        setHasMore(true);
        await loadMoreTweets(id, page);
    };

    const loadMoreTweets = async (id: number, page: number) => {
        const currentTweets = await getTweets(id, page);

        console.log(currentTweets);
        console.log(tweets);
        console.log(id);
        // console.log(hasMore);
        // console.log(page);

        setTweets(prev => [...prev, ...currentTweets]);
        setPage(page + 1);
        setHasMore(currentTweets.length > 0);

        // console.log(currentTweets.length > 0);
        // console.log(page);
    };

    return (
        <InfiniteScroll
            dataLength={tweets.length}
            next={() => loadMoreTweets(parseInt(query), page)}
            hasMore={hasMore}
            loader={<h4>Loading...</h4>}
            endMessage={
                <p style={{ textAlign: 'center', marginTop: '20px' }}>
                    <b>You have seen all tweets</b>
                </p>
            }
        >
            <Grid container spacing={2}>
                {tweets.map(tweet => (
                    <Grid item key={tweet.id} xs={8}>
                        <Link href={`/tweet/display?id=${tweet.id}`}>
                            <Card>
                                <CardActionArea>
                                    <CardContent>
                                        <Typography gutterBottom variant="h5" component="div">
                                            {tweet.message}
                                        </Typography>
                                    </CardContent>
                                    {tweet.mediaType && (tweet.mediaType === 'image/jpeg' || tweet.mediaType === 'image/png' || tweet.mediaType === 'image/gif') && (
                                        <Link href={tweet.getUploadedImageUrl()}>
                                            <CardMedia
                                                component="img"
                                                image={tweet.getThumbnailUrl()}
                                                alt="tweet image"
                                                sx={{
                                                    height: 200,
                                                    width: 1,
                                                    objectFit: 'contain'
                                                }}
                                            />
                                        </Link>
                                    )}
                                    {tweet.mediaType && (tweet.mediaType === 'video/mp4' || tweet.mediaType === 'video/webm' || tweet.mediaType === 'video/ogg') && (
                                        <CardMedia
                                            component="video"
                                            controls
                                            sx={{
                                                height: 200,
                                                width: 1,
                                                objectFit: 'contain'
                                            }}
                                            src={tweet.getUploadedVideoUrl()}
                                        />
                                    )}
                                    <IconButton aria-label="add to favorites">
                                        <FavoriteBorderIcon /> {tweet.getLikeCount()}
                                    </IconButton>
                                    <IconButton aria-label="retweet">
                                        <RepeatIcon /> {tweet.getRetweetCount()}
                                    </IconButton>
                                    <IconButton aria-label="reply">
                                        <ChatBubbleOutlineIcon /> {tweet.getReplyCount()}
                                    </IconButton>
                                </CardActionArea>
                            </Card>
                        </Link>
                    </Grid>
                ))}
            </Grid>
        </InfiniteScroll>
    );
};

export default Tweets;
