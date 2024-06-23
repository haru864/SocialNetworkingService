import React, { useState, useEffect, useRef } from 'react';
import {
    Box, TextField, Button, List, CircularProgress
} from '@mui/material';
import SendIcon from '@mui/icons-material/Send';
import { useSearchParams } from 'next/navigation';
import { Message } from './ChatInfo';
import { MessageItem } from './MessageItem';
import { getUserinfo } from '../../../common/UserInfoFunctions';
import { UserInfo } from '@/app/common/UserInfo';
import { getChatInfo, handleSendMessage } from './ChatHistoryFunctions';

const ChatHistory: React.FC = () => {
    const [loginUser, setLoginUser] = useState<UserInfo | null>(null);
    const [chatPartner, setChatPartner] = useState<UserInfo | null>(null);

    const [messages, setMessages] = useState<Message[]>([]);
    const [page, setPage] = useState<number>(1);
    const hasMoreMessagesRef = useRef<boolean>(true);

    const messagesEndRef = useRef<HTMLDivElement>(null);
    const didInitialScroll = useRef(false);
    const listRef = useRef<HTMLUListElement>(null);
    const scrollHeightBeforeLoadRef = useRef<number>(0);
    const shouldScrollToBottom = useRef(false);

    const searchParams = useSearchParams();
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const query = searchParams.get('user_id');
        if (query === null) {
            return;
        }
        const chatPartnerId = Number(query);
        initializeChatData(chatPartnerId);
    }, []);

    useEffect(() => {
        if (chatPartner === null || chatPartner === undefined) {
            return;
        }
        const eventSource = new EventSource(`${process.env.SSE_MESSAGE_URL}?login_user_id=${loginUser?.id}&recipient_user_id=${chatPartner?.id}`);
        eventSource.onmessage = (event: MessageEvent) => {
            const data = JSON.parse(event.data);
            const message = new Message(data);
            if (listRef.current) {
                const isAtBottom = ((listRef.current.scrollHeight - listRef.current.scrollTop) === listRef.current.clientHeight);
                shouldScrollToBottom.current = isAtBottom;
                setMessages(prev => [...prev, message]);
            }
        };
        eventSource.onerror = (error) => {
            console.error('EventSource failed:', error);
            eventSource.close();
        };
        return () => {
            eventSource.close();
        };
    }, [chatPartner]);

    useEffect(() => {
        const isInitialLoading = (messages.length > 0 && !didInitialScroll.current);
        if (isInitialLoading) {
            scrollToBottom();
            didInitialScroll.current = true;
            return;
        }
        if (listRef.current && shouldScrollToBottom.current) {
            scrollToBottom();
            shouldScrollToBottom.current = false;
            return;
        }

        const currentScrollHeight = listRef.current?.scrollHeight ?? 0;
        console.log(scrollHeightBeforeLoadRef.current);
        console.log(currentScrollHeight);
        const newMessagesScrollHeight = currentScrollHeight - scrollHeightBeforeLoadRef.current;
        if (listRef !== null && listRef.current !== null) {
            listRef.current.scrollTop = newMessagesScrollHeight;
        }



    }, [messages]);

    const initializeChatData = async (chatPartnerId: number) => {
        setLoading(true);
        const chatInfo = await getChatInfo(chatPartnerId, page);
        const loginUserInfo = await getUserinfo(chatInfo.loginUser.id);
        const chatPartnerInfo = await getUserinfo(chatInfo.chatPartner.id);
        setLoginUser(loginUserInfo);
        setChatPartner(chatPartnerInfo);
        setMessages(chatInfo.messages);
        setLoading(false);
    };

    const loadMoreMessages = async (): Promise<void> => {
        if (chatPartner === null) {
            return;
        }
        if (hasMoreMessagesRef.current === false) {
            return;
        }
        const chatInfo = await getChatInfo(chatPartner.id, page);
        if (chatInfo.messages.length === 0) {
            return;
        }
        setMessages(prev => [...chatInfo.messages, ...prev]);
        setPage(page + 1);
        hasMoreMessagesRef.current = (chatInfo.messages.length === 20);
    };

    const handleScroll = async (e: React.UIEvent<HTMLUListElement>) => {
        if (e.currentTarget.scrollTop === 0) {
            scrollHeightBeforeLoadRef.current = listRef.current?.scrollHeight ?? 0;
            await loadMoreMessages();
        }
    };

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView();
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
                <List ref={listRef} sx={{ flex: 1, overflowY: 'auto' }} onScroll={handleScroll}>
                    {messages.map((message) => (
                        <MessageItem key={message.id} message={message} loginUser={loginUser} chatPartner={chatPartner} />
                    ))}
                    <div ref={messagesEndRef} />
                </List>
                <Box component="form" onSubmit={(e) => handleSendMessage(e, chatPartner.id)} sx={{ padding: 2 }}>
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
