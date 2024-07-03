import React, { useState, useEffect } from 'react';
import {
    Grid, Card, CardContent, Typography, CardActionArea
} from '@mui/material';
import Link from 'next/link';
import { NotificationDTO } from './NotificationDTO';
import { UserInfo } from '@/app/common/UserInfo';
import { Like } from './Like';
import { Follow } from './Follow';
import { Tweet } from '@/app/common/Tweet';
import { getTweet, getUserInfo } from '@/app/common/TweetCardFunctions';
import { getFollowData, getLikeDataByLikeId, getMessageData } from './NotificationFunctions';
import { Message } from '@/app/messages/chatlist/components/Chat';

interface NotificationCardProps {
    notificationDTO: NotificationDTO;
}

const NotificationCard: React.FC<NotificationCardProps> = ({ notificationDTO }) => {
    const [counterpartUserInfo, setCounterpartUserInfo] = useState<UserInfo | null>(null);
    const [like, setLike] = useState<Like | null>(null);
    const [follow, setFollow] = useState<Follow | null>(null);
    const [replyTweet, setReplyTweet] = useState<Tweet | null>(null);
    const [retweet, setRetweet] = useState<Tweet | null>(null);
    const [message, setMessage] = useState<Message | null>(null);

    useEffect(() => {
        switch (notificationDTO.notificationType) {
            case 'like':
                loadLike();
                break;
            case 'follow':
                loadFollow();
                break;
            case 'reply':
                loadReplyTweet();
                break;
            case 'retweet':
                loadRetweet();
                break;
            case 'message':
                loadMessage();
                break;
            default:
                break;
        }
    }, []);

    useEffect(() => {
        if (like !== null) loadUserInfo(like.userId);
        else if (follow !== null) loadUserInfo(follow.followerId);
        else if (replyTweet !== null) loadUserInfo(replyTweet.userId);
        else if (retweet !== null) loadUserInfo(retweet.userId);
        else if (message !== null) loadUserInfo(message.senderId);
    }, [like, follow, replyTweet, retweet, message]);

    const loadLike = async () => {
        const like = await getLikeDataByLikeId(notificationDTO.entityId);
        setLike(like);
    };
    const loadFollow = async () => {
        const follow = await getFollowData(notificationDTO.entityId);
        setFollow(follow);
    };
    const loadReplyTweet = async () => {
        const replyTweet = await getTweet(notificationDTO.entityId);
        setReplyTweet(replyTweet);
    };
    const loadRetweet = async () => {
        const retweet = await getTweet(notificationDTO.entityId);
        setRetweet(retweet);
    };
    const loadMessage = async () => {
        const message = await getMessageData(notificationDTO.entityId);
        setMessage(message);
    };
    const loadUserInfo = async (userId: number) => {
        const userInfo = await getUserInfo(userId);
        setCounterpartUserInfo(userInfo);
    };

    const confirmedStyle = { color: 'black' };
    const unconfirmedStyle = { color: 'blue', fontWeight: 'bold' };

    return (
        <Grid item key={notificationDTO.id} xs={8}>
            <Card>
                <CardActionArea>
                    {notificationDTO.notificationType === 'like' && (
                        <Link href={`/tweet/display?id=${like?.tweetId}`}>
                            <CardContent>
                                <Typography variant="body2"
                                    style={notificationDTO.isConfirmed ? confirmedStyle : unconfirmedStyle}>
                                    {counterpartUserInfo?.username} liked your tweet.
                                </Typography>
                            </CardContent>
                        </Link>
                    )}
                    {notificationDTO.notificationType === 'retweet' && (
                        <Link href={`/tweet/display?id=${retweet?.id}`}>
                            <CardContent>
                                <Typography variant="body2"
                                    style={notificationDTO.isConfirmed ? confirmedStyle : unconfirmedStyle}>
                                    {counterpartUserInfo?.username} retweeted your tweet.
                                </Typography>
                            </CardContent>
                        </Link>
                    )}
                    {notificationDTO.notificationType === 'reply' && (
                        <Link href={`/tweet/display?id=${replyTweet?.id}`}>
                            <CardContent>
                                <Typography variant="body2"
                                    style={notificationDTO.isConfirmed ? confirmedStyle : unconfirmedStyle}>
                                    {counterpartUserInfo?.username} replied to your tweet.
                                </Typography>
                            </CardContent>
                        </Link>
                    )}
                    {notificationDTO.notificationType === 'follow' && (
                        <Link href={`/userinfo?id=${counterpartUserInfo?.id}`}>
                            <CardContent>
                                <Typography variant="body2"
                                    style={notificationDTO.isConfirmed ? confirmedStyle : unconfirmedStyle}>
                                    {counterpartUserInfo?.username} has followed you.
                                </Typography>
                            </CardContent>
                        </Link>
                    )}
                    {notificationDTO.notificationType === 'message' && (
                        <Link href={`/messages/chatroom?user_id=${counterpartUserInfo?.id}`}>
                            <CardContent>
                                <Typography variant="body2"
                                    style={notificationDTO.isConfirmed ? confirmedStyle : unconfirmedStyle}>
                                    {counterpartUserInfo?.username} has sent you a message.
                                </Typography>
                            </CardContent>
                        </Link>
                    )}
                    <CardContent>
                        <Typography variant="body2">
                            {notificationDTO.createdAt}
                        </Typography>
                    </CardContent>
                </CardActionArea>
            </Card>
        </Grid>
    );
};

export default NotificationCard;
