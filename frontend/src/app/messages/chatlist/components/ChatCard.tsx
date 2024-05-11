import React, { useState, useEffect } from 'react';
import {
    Grid, Card, CardContent, Typography, CardMedia, CardActionArea,
    Box, CircularProgress
} from '@mui/material';
import Link from 'next/link';
import { UserInfo } from '../../../common/UserInfo';
import { getUserInfo } from '@/app/common/TweetCardFunctions';
import { Chat } from './Chat';

interface ChatCardProps {
    chat: Chat;
}

const ChatCard: React.FC<ChatCardProps> = ({ chat }) => {
    const [chatPartnerInfo, setChatPartnerInfo] = useState<UserInfo | null>(null);

    useEffect(() => {
        loadChatPartnerInfo(chat.chatPartner.id);
    }, []);

    const loadChatPartnerInfo = async (userId: number) => {
        const currUserInfo = await getUserInfo(userId);
        setChatPartnerInfo(currUserInfo);
    };

    if (chatPartnerInfo === null) {
        return (
            <Grid item xs={8}>
                <Box display="flex" justifyContent="center" alignItems="center" height="100vh">
                    <CircularProgress />
                </Box>
            </Grid>
        );
    } else {
        return (
            <Grid item key={chat.chatPartner.id} xs={8}>
                <Link href={`/messages/chatroom?user_id=${chat.chatPartner.id}`}>
                    <Card>
                        <CardActionArea>
                            <CardContent sx={{ padding: 1 }}>
                                <Grid container alignItems="center" spacing={2}>
                                    <Grid item>
                                        <Link href={`/userinfo?id=${chat.chatPartner.id}`}>
                                            <CardMedia
                                                component="img"
                                                image={chatPartnerInfo.getThumbnailUrl()}
                                                alt="profile image"
                                                sx={{
                                                    height: 70,
                                                    width: 1,
                                                    objectFit: 'contain'
                                                }}
                                            />
                                        </Link>
                                    </Grid>
                                    <Grid item>
                                        <Typography variant="h4" color="text.primary">
                                            {chatPartnerInfo.username}
                                        </Typography>
                                    </Grid>
                                </Grid>
                                <Typography variant="body1" color="text.disabled">
                                    {chat.latestMessage.message}
                                </Typography>
                            </CardContent>
                        </CardActionArea>
                    </Card>
                </Link>
            </Grid >
        );
    }
};

export default ChatCard;
