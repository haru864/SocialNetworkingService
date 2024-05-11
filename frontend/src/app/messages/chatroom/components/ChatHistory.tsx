import React, { useState, useEffect, useRef } from 'react';
import {
    Box, TextField, Button, List, ListItem, ListItemText, ListItemAvatar,
    Avatar, Typography, CircularProgress
} from '@mui/material';
import SendIcon from '@mui/icons-material/Send';
import { useSearchParams } from 'next/navigation';
import { ChatInfo, ChatUser, Message } from './ChatInfo';
import { MessageItem } from './MessageItem';

async function getChatInfo(chatPartnerId: number, page: number): Promise<ChatInfo> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/messages/${chatPartnerId}?page=${page}&limit=20`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        const loginUserInfo = new ChatUser(jsonData['login_user']);
        const chatPartnerInfo = new ChatUser(jsonData['chat_partner']);
        const messageList = jsonData['messages'];
        let messages: Message[] = [];
        for (const message of messageList) {
            messages.push(new Message(message));
        }
        return new ChatInfo(loginUserInfo, chatPartnerInfo, messages);
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

async function handleSendMessage(
    event: React.FormEvent<HTMLFormElement>,
    setLoading: React.Dispatch<React.SetStateAction<boolean>>
) {
    try {
        event.preventDefault();
        console.log('メッセージ送信');
        // const data: FormData = new FormData(event.currentTarget);
        // let dateTime: string | null = data.get('dateTime') as string | null;
        // if (dateTime) {
        //     dateTime = dateTime.replace('T', ' ') + ':00';
        //     data.set('dateTime', dateTime);
        // }
        // const message: string = data.get('message') as string;
        // ValidationUtil.validateCharCount(message, "Message", 1, 200);
        // setLoading(true);
        // const response = await fetch(`${process.env.API_DOMAIN}/api/tweets`, {
        //     method: 'POST',
        //     body: data
        // });
        // setLoading(false);
        // if (!response.ok) {
        //     const responseData = await response.json();
        //     throw new Error(responseData["error_message"]);
        // }
        // alert('Tweet posted.');
    } catch (error: any) {
        console.error(error);
        alert(error);
    }
}

// TODO 画面表示時にデフォルトで下にスクロールする/チャットっぽい見た目にする/SSE対応
const ChatHistory: React.FC = () => {
    const [loginUser, setLoginUser] = useState<ChatUser | null>(null);
    const [chatPartner, setChatPartner] = useState<ChatUser | null>(null);
    const [messages, setMessages] = useState<Message[]>([]);
    const [page, setPage] = useState<number>(1);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const listRef = useRef<HTMLUListElement>(null);
    const searchParams = useSearchParams();
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const query = searchParams.get('user_id');
        if (query === null) {
            return;
        }
        const chatPartnerId = Number(query);
        initializeChatData(chatPartnerId);
        scrollToBottom();
    }, []);

    const initializeChatData = async (chatPartnerId: number) => {
        const chatInfo = await getChatInfo(chatPartnerId, page);
        setLoginUser(chatInfo.loginUser);
        setChatPartner(chatInfo.chatPartner);
        setMessages(chatInfo.messages);
    };

    const loadMoreMessages = async () => {
        if (chatPartner === null) {
            return;
        }
        const chatInfo = await getChatInfo(chatPartner.id, page);
        setMessages(prev => [...chatInfo.messages, ...prev]);
        setPage(page + 1);
    };

    const handleScroll = (e: React.UIEvent<HTMLUListElement>) => {
        if (e.currentTarget.scrollTop === 0) {
            loadMoreMessages();
        }
    };

    const scrollToBottom = () => {
        if (listRef.current) {
            // listRef.current.scrollTop = listRef.current.scrollHeight;
            listRef.current.scrollIntoView();
        }
    };

    if (loading) {
        return (
            <Box display="flex" justifyContent="center" alignItems="center" height="100vh">
                <CircularProgress />
            </Box>
        );
    } else if (loginUser === null || chatPartner === null) {
        return;
    } else {
        return (
            <Box sx={{ display: 'flex', flexDirection: 'column', height: '90vh' }}>
                {/* <List ref={listRef} sx={{ flex: 1, overflowY: 'auto' }} onScroll={handleScroll}>
                {messages.map((message) => (
                    <ListItem key={message.id} alignItems="flex-start">
                        <ListItemAvatar>
                            <Avatar alt={`Sender ${message.senderId}`} />
                        </ListItemAvatar>
                        <ListItemText
                            primary={<Typography color="text.primary">{message.message}</Typography>}
                            secondary={message.sendDatetime}
                        />
                    </ListItem>
                ))}
            </List> */}
                <List ref={listRef} sx={{ flex: 1, overflowY: 'auto' }}>
                    {messages.map((message) => (
                        <MessageItem key={message.id} message={message} loginUserId={loginUser.id} chatPartnerId={chatPartner.id} />
                    ))}
                </List>
                <Box component="form" onSubmit={(e) => handleSendMessage(e, setLoading)} sx={{ padding: 2 }}>
                    <TextField
                        fullWidth
                        name="message"
                        label="Message"
                        id="message"
                        autoComplete=""
                        multiline
                        minRows={3}
                        maxRows={5}
                    />
                    <input
                        accept="image/jpeg, image/png, image/gif, video/mp4, video/webm, video/ogg"
                        type="file"
                        id="upload_file_button"
                        name="media"
                        style={{ display: 'none' }}
                    />
                    <label htmlFor="upload_file_button">
                        <Button variant="contained" component="span">
                            Upload Image / Video
                        </Button>
                    </label>
                    <Button type="submit" variant="contained" endIcon={<SendIcon />} sx={{ margin: 1 }}>
                        Send
                    </Button>
                </Box>
            </Box>
        );
    }
}

export default ChatHistory;
