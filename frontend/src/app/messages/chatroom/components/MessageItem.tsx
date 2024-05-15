import React from 'react';
import {
    ListItem, ListItemText, ListItemAvatar,
    Avatar, Typography, Box
} from '@mui/material';
import Link from 'next/link';
import { Message } from './ChatInfo';
import { UserInfo } from '@/app/common/UserInfo';

interface MessageProps {
    message: Message;
    loginUser: UserInfo;
    chatPartner: UserInfo;
}

export const MessageItem: React.FC<MessageProps> = ({ message, loginUser, chatPartner }) => {
    const isLoginUser = message.senderId === loginUser.id;

    const avatarSize = {
        width: 70,
        height: 70,
        fontSize: '1.25rem'
    };

    return (
        <ListItem
            sx={{
                display: 'flex',
                flexDirection: 'row',
                justifyContent: isLoginUser ? 'flex-end' : 'flex-start',
                mb: 1,
            }}
        >
            {!isLoginUser && (
                <Box sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center', mr: 2 }}>
                    <ListItemAvatar>
                        <Avatar
                            alt={`Sender ${chatPartner.username}`}
                            src={chatPartner.getThumbnailUrl()}
                            sx={{ ...avatarSize }}
                        />
                    </ListItemAvatar>
                    <ListItemText
                        primary={chatPartner.username}
                        primaryTypographyProps={{ variant: 'body2', color: 'text.primary' }}
                    />
                </Box>
            )
            }
            <Box
                sx={{
                    maxWidth: '60%',
                    bgcolor: isLoginUser ? 'primary.light' : 'grey.200',
                    p: 1,
                    borderRadius: '10px',
                    border: '1px solid grey',
                    wordWrap: 'break-word'
                }}
            >
                <ListItemText
                    primary={
                        <Typography
                            color={isLoginUser ? 'primary.contrastText' : 'text.primary'}
                            sx={{ wordWrap: 'break-word' }}
                        >
                            {message.message}
                        </Typography>
                    }
                    secondary={message.sendDatetime}
                    sx={{
                        textAlign: isLoginUser ? 'right' : 'left',
                        order: isLoginUser ? 1 : 0,
                    }}
                />
                {message.mediaType && (message.mediaType === 'image/jpeg' || message.mediaType === 'image/png' || message.mediaType === 'image/gif') && (
                    <Link href={message.getUploadedImageUrl()}>
                        <img
                            src={message.getThumbnailUrl()}
                            alt="Attached media"
                            style={{
                                height: 200,
                                width: 200,
                                objectFit: 'contain'
                            }}
                        />
                    </Link>
                )}
                {message.mediaType && (message.mediaType === 'video/mp4' || message.mediaType === 'video/webm' || message.mediaType === 'video/ogg') && (
                    // <CardMedia
                    //     component="video"
                    //     controls
                    //     sx={{
                    //         height: 200,
                    //         width: 1,
                    //         objectFit: 'contain'
                    //     }}
                    //     src={message.getUploadedVideoUrl()}
                    // />
                    <video controls style={{ maxWidth: '100%', height: 'auto' }}>
                        <source src={message.getUploadedVideoUrl()} type={message.mediaType} />
                        Your browser does not support the video tag.
                    </video>
                )}
            </Box>
        </ListItem >
    );
};
