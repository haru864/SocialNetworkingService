import React, { useState, useEffect } from 'react';
import InfiniteScroll from 'react-infinite-scroll-component';
import { Grid } from '@mui/material';
import ChatCard from './ChatCard';
import { Chat, ChatPartner, Message } from './Chat';

async function getAllChats(page: number): Promise<Chat[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/messages?page=${page}&limit=20`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        const chatArray = jsonData['chats'];
        let chatList = [];
        for (const chatData of chatArray) {
            if (chatData !== null) {
                const chatPartner = new ChatPartner(chatData['chat_partner']);
                const latestMessage = new Message(chatData['latest_message']);
                const chat = new Chat(chatPartner, latestMessage);
                chatList.push(chat);
            }
        }
        return chatList;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

const ChatList: React.FC = () => {
    const [chats, setChats] = useState<Chat[]>([]);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [page, setPage] = useState<number>(1);

    useEffect(() => {
        loadMoreChats();
    }, []);

    const loadMoreChats = async () => {
        const currentChats = await getAllChats(page);
        setChats(prev => [...prev, ...currentChats]);
        setPage(page + 1);
        setHasMore(currentChats.length === 20);
    };

    if (chats.length === 0 && !hasMore) {
        return (
            <p style={{ textAlign: 'center', marginTop: '20px' }}>
                <b>No messages have been exchanged yet</b>
            </p>
        );
    }

    return (
        <InfiniteScroll
            dataLength={chats.length}
            next={loadMoreChats}
            hasMore={hasMore}
            loader={<h4>Loading...</h4>}
            endMessage={
                <p style={{ textAlign: 'center', marginTop: '20px' }}>
                    <b>You have seen all tweets</b>
                </p>
            }
        >
            <Grid container spacing={2}>
                {chats.map(chat => (
                    <ChatCard key={chat.chatPartner.id} chat={chat} />
                ))}
            </Grid>
        </InfiniteScroll>
    );
};

export default ChatList;
