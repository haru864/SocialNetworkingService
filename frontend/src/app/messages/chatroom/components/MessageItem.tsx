import React, { useState, useEffect, useRef } from 'react';
import {
    ListItem, ListItemText, ListItemAvatar,
    Avatar, Typography, Box
} from '@mui/material';
import { Message } from './ChatInfo';

interface MessageProps {
    message: Message;
    loginUserId: number;
    chatPartnerId: number;
}

export const MessageItem: React.FC<MessageProps> = ({ message, loginUserId, chatPartnerId }) => {
    const isLoginUser = message.senderId === loginUserId;
    return (
        <ListItem
            sx={{
                display: 'flex',
                flexDirection: 'row',
                justifyContent: isLoginUser ? 'flex-end' : 'flex-start',
                mb: 1,  // メッセージ間のマージンを追加
            }}
        >
            {!isLoginUser && (
                <ListItemAvatar>
                    <Avatar alt={`Sender ${message.senderId}`} />
                </ListItemAvatar>
            )}
            <Box
                sx={{
                    maxWidth: '60%',
                    bgcolor: isLoginUser ? 'primary.light' : 'grey.200',  // 背景色
                    p: 1,  // パディング
                    borderRadius: '10px',  // 角の丸み
                    border: '1px solid grey',  // 枠線
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
                        order: isLoginUser ? 1 : 0, // テキストをアバターの前または後ろに移動
                    }}
                />
            </Box>
        </ListItem>
    );
};
