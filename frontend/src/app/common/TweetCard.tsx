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
    getTweet, handleLike, checkRetweetedOrNot, addRetweet, removeRetweet,
    addReply, deleteTweet,
    getUserInfo
} from './TweetCardFunctions';
import { UserInfo } from './UserInfo';

interface TweetCardProps {
    tweetId: number;
}

const TweetCard: React.FC<TweetCardProps> = ({ tweetId }) => {
    const [isVisible, setIsVisible] = useState(true);
    const [tweet, setTweet] = useState<Tweet | null>(null);

    const [retweetMessage, setRetweetMessage] = useState<string>('');
    const [showRetweetForm, setShowRetweetForm] = useState<boolean>(false);

    const [replyText, setReplyText] = useState<string>('');
    const [replyFile, setReplyFile] = useState<File | null>(null);
    const [showReplyForm, setShowReplyForm] = useState<boolean>(false);

    const [userInfo, setUserInfo] = useState<UserInfo | null>(null);

    useEffect(() => {
        loadTweet(tweetId);
    }, []);

    const loadTweet = async (tweetId: number) => {
        const currTweet = await getTweet(tweetId);
        setTweet(currTweet);
        const currUserInfo = await getUserInfo(currTweet.userId);
        setUserInfo(currUserInfo);
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

    const openReplyDialog = () => setShowReplyForm(true);
    const closeReplyDialog = () => setShowReplyForm(false);

    const handleTextChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        setReplyText(event.target.value);
    };
    const handleFileChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        if (event.target.files && event.target.files.length > 0) {
            setReplyFile(event.target.files[0]);
        }
    };

    if (!isVisible) {
        return null;
    }

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
                        {userInfo !== null && (
                            <Link href={`/userinfo?id=${userInfo.id}`}>
                                <CardContent sx={{ padding: 1 }}>
                                    <Grid container alignItems="center" spacing={2}>
                                        {userInfo.getThumbnailUrl() && (
                                            <Grid item>
                                                <CardMedia
                                                    component="img"
                                                    image={userInfo.getThumbnailUrl()}
                                                    alt="profile image"
                                                    sx={{
                                                        height: 70,
                                                        width: 1,
                                                        objectFit: 'contain'
                                                    }}
                                                />
                                            </Grid>
                                        )}
                                        <Grid item>
                                            <Typography variant="h4" color="text.primary">
                                                {userInfo.username}
                                            </Typography>
                                        </Grid>
                                    </Grid>
                                </CardContent>
                            </Link>
                        )}
                        {tweet.replyToId !== null && (
                            <Link href={`/tweet/display?id=${tweet.replyToId}`}>
                                <CardContent sx={{ padding: 1 }}>
                                    <Typography variant="body1" color="primary.main">
                                        Link: Reply Source Tweet
                                    </Typography>
                                </CardContent>
                            </Link>
                        )}
                        {tweet.retweetToId !== null && (
                            <Link href={`/tweet/display?id=${tweet.retweetToId}`}>
                                <CardContent sx={{ padding: 1 }}>
                                    <Typography variant="body1" color="primary.main">
                                        Link: Retweet Source Tweet
                                    </Typography>
                                </CardContent>
                            </Link>
                        )}
                        <Link href={`/tweet/display?id=${tweet.id}`}>
                            <CardContent>
                                <Typography
                                    variant="h5"
                                    component="div"
                                    style={{
                                        wordWrap: 'break-word',
                                        whiteSpace: 'pre-wrap',
                                        overflowWrap: 'break-word'
                                    }}
                                >
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
                        <CardContent>
                            <Typography variant="body1">
                                Posted at {tweet.postingDatetime}
                            </Typography>
                        </CardContent>
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
                        <IconButton aria-label="reply" onClick={openReplyDialog}>
                            <ChatBubbleOutlineIcon /> {tweet.getReplyCount()}
                        </IconButton>
                        <IconButton aria-label="delete" onClick={
                            async () => {
                                await deleteTweet(tweetId);
                                setIsVisible(false);
                            }
                        }>
                            <DeleteForeverIcon />
                        </IconButton>
                        <Dialog open={showRetweetForm} onClose={closeRetweetDialog}>
                            <DialogTitle>Retweet</DialogTitle>
                            <DialogContent>
                                <DialogContentText>
                                    Write Retweet Message
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
                        <Dialog open={showReplyForm} onClose={closeReplyDialog}>
                            <DialogTitle>Reply</DialogTitle>
                            <DialogContent>
                                <Grid item xs={12}>
                                    <TextField
                                        fullWidth
                                        name="message"
                                        label="Message"
                                        id="message"
                                        autoComplete=""
                                        multiline
                                        minRows={3}
                                        maxRows={5}
                                        value={replyText}
                                        onChange={handleTextChange}
                                    />
                                </Grid>
                                <Grid item xs={12} style={{ display: 'flex', justifyContent: 'center' }}>
                                    <input
                                        accept="image/jpeg, image/png, image/gif, video/mp4, video/webm, video/ogg"
                                        type="file"
                                        id="upload_file_button"
                                        name="media"
                                        style={{ display: 'none' }}
                                        onChange={handleFileChange}
                                    />
                                    <label htmlFor="upload_file_button">
                                        <Button variant="contained" component="span">
                                            Upload Image / Video
                                        </Button>
                                    </label>
                                </Grid>
                            </DialogContent>
                            <DialogActions>
                                <Button onClick={async () => {
                                    await addReply(tweetId, replyText, replyFile);
                                    closeReplyDialog();
                                    loadTweet(tweet.id);
                                }}>Reply</Button>
                                <Button onClick={closeReplyDialog}>Cancel</Button>
                            </DialogActions>
                        </Dialog>
                    </CardActionArea>
                </Card>
            </Grid>
        );
    }
};

export default TweetCard;
