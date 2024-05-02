import React, { useState, useEffect } from 'react';
import {
    Grid,
    Card,
    CardContent,
    Typography,
    CardMedia,
    CardActionArea,
    IconButton,
    Box,
    CircularProgress
} from '@mui/material';
import FavoriteBorderIcon from '@mui/icons-material/FavoriteBorder';
import ChatBubbleOutlineIcon from '@mui/icons-material/ChatBubbleOutline';
import RepeatIcon from '@mui/icons-material/Repeat';
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import Link from 'next/link';
import { Tweet } from '@/app/common/Tweet';
import { getTweet, handleLike } from './TweetCardFunctions';

interface TweetCardProps {
    tweetId: number;
}

const TweetCard: React.FC<TweetCardProps> = ({ tweetId }) => {
    const [tweet, setTweet] = useState<Tweet | null>(null);

    useEffect(() => {
        loadTweet(tweetId);
        console.log('useEffect');
    }, []);

    const loadTweet = async (tweetId: number) => {
        const currTweet = await getTweet(tweetId);
        setTweet(currTweet);
    };

    if (tweet === null) {
        return (
            <Grid item xs={8}>
                <Box display="flex" justifyContent="center" alignItems="center" height="100vh">
                    <CircularProgress />
                </Box>
            </Grid>
        );
    } else {
        return (
            <Grid item key={tweet.id} xs={8}>
                <Card>
                    <CardActionArea>
                        <Link href={`/tweet/display?id=${tweet.id}`}>
                            <CardContent>
                                <Typography gutterBottom variant="h5" component="div">
                                    {tweet.message}
                                </Typography>
                            </CardContent>
                        </Link>
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
                        <IconButton aria-label="like" onClick={
                            async () => {
                                await handleLike(tweet.id);
                                loadTweet(tweet.id);
                            }
                        }>
                            <FavoriteBorderIcon /> {tweet.getLikeCount()}
                        </IconButton>
                        <IconButton aria-label="retweet">
                            <RepeatIcon /> {tweet.getRetweetCount()}
                        </IconButton>
                        <IconButton aria-label="reply">
                            <ChatBubbleOutlineIcon /> {tweet.getReplyCount()}
                        </IconButton>
                        <IconButton aria-label="delete">
                            <DeleteForeverIcon />
                        </IconButton>
                    </CardActionArea>
                </Card>
            </Grid>
        );
    }
};

export default TweetCard;
