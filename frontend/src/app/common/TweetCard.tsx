import React, { useState, useEffect } from 'react';
import {
    Grid, Card, CardContent, Typography, CardMedia, CardActionArea,
    IconButton, Box, CircularProgress, TextField, Button,
    Dialog, DialogTitle, DialogContent, DialogContentText, DialogActions
} from '@mui/material';
import FavoriteBorderIcon from '@mui/icons-material/FavoriteBorder';
import ChatBubbleOutlineIcon from '@mui/icons-material/ChatBubbleOutline';
import RepeatIcon from '@mui/icons-material/Repeat';
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import Link from 'next/link';
import { Tweet } from '@/app/common/Tweet';
import {
    getTweet, handleLike, checkRetweetedOrNot, addRetweet, removeRetweet
} from './TweetCardFunctions';

interface TweetCardProps {
    tweetId: number;
}

const TweetCard: React.FC<TweetCardProps> = ({ tweetId }) => {
    const [tweet, setTweet] = useState<Tweet | null>(null);
    const [retweetMessage, setRetweetMessage] = useState('');
    const [showRetweetForm, setShowRetweetForm] = useState(false);

    useEffect(() => {
        loadTweet(tweetId);
    }, []);

    const loadTweet = async (tweetId: number) => {
        const currTweet = await getTweet(tweetId);
        setTweet(currTweet);
    };

    const openRetweetDialog = async (tweetId: number) => {
        const isRetweeted = await checkRetweetedOrNot(tweetId);
        if (isRetweeted) {
            await removeRetweet(tweetId);
            loadTweet(tweetId);
        } else {
            setShowRetweetForm(true);
        }
    };
    const closeRetweetDialog = () => setShowRetweetForm(false);

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
                        <IconButton aria-label="retweet" onClick={() => openRetweetDialog(tweetId)}>
                            <RepeatIcon /> {tweet.getRetweetCount()}
                        </IconButton>
                        <IconButton aria-label="reply">
                            <ChatBubbleOutlineIcon /> {tweet.getReplyCount()}
                        </IconButton>
                        <IconButton aria-label="delete">
                            <DeleteForeverIcon />
                        </IconButton>
                        <Dialog open={showRetweetForm} onClose={closeRetweetDialog}>
                            <DialogTitle>リツイート</DialogTitle>
                            <DialogContent>
                                <DialogContentText>
                                    リツイートにコメントを追加してください。
                                </DialogContentText>
                                <TextField
                                    autoFocus
                                    margin="dense"
                                    id="retweet_message"
                                    label="Retweet Message"
                                    type="text"
                                    fullWidth
                                    variant="outlined"
                                    value={retweetMessage}
                                    onChange={(e) => setRetweetMessage(e.target.value)}
                                    multiline
                                    minRows={3}
                                    maxRows={5}
                                />
                            </DialogContent>
                            <DialogActions>
                                <Button onClick={async () => {
                                    await addRetweet(tweetId, retweetMessage);
                                    closeRetweetDialog();
                                    loadTweet(tweet.id);
                                }}>Retweet</Button>
                                <Button onClick={closeRetweetDialog}>Cancel</Button>
                            </DialogActions>
                        </Dialog>
                    </CardActionArea>
                </Card>
            </Grid>
        );
    }
};

export default TweetCard;
