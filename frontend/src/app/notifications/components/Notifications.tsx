import React, { useState, useEffect } from 'react';
import InfiniteScroll from 'react-infinite-scroll-component';
import { Grid } from '@mui/material';
import { NotificationDTO } from './NotificationDTO';
import { getNotifications } from './NotificationFunctions';
import NotificationCard from './NotificationCard';

const Notifications: React.FC = () => {
    const [notificationDTOs, setNotificationDTOs] = useState<NotificationDTO[]>([]);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [page, setPage] = useState<number>(1);

    useEffect(() => {
        loadNotifications();
        getNotificationInRealTime();
    }, []);

    const loadNotifications = async () => {
        const currNotifications = await getNotifications(page);
        setNotificationDTOs(prev => [...prev, ...currNotifications]);
        setPage(page + 1);
        const maxContentsPerPage: number = 20;
        setHasMore(currNotifications.length === maxContentsPerPage);
    };

    const getNotificationInRealTime = () => {
        const eventSource = new EventSource(`${process.env.API_DOMAIN}/api/live/notifications`);
        eventSource.onmessage = (event: MessageEvent) => {
            const data = JSON.parse(event.data);
            const notificationDTO = new NotificationDTO(data);
            setNotificationDTOs(prev => [notificationDTO, ...prev]);
        };
        eventSource.onerror = (error) => {
            console.error('EventSource failed:', error);
            eventSource.close();
        };
        return () => {
            eventSource.close();
        };
    };

    return (
        <InfiniteScroll
            dataLength={notificationDTOs.length}
            next={loadNotifications}
            hasMore={hasMore}
            loader={<h4>Loading...</h4>}
            endMessage={
                <p style={{ textAlign: 'center', marginTop: '20px' }}>
                    <b>You have seen all notifications</b>
                </p>
            }
        >
            <Grid container spacing={2}>
                {notificationDTOs.map(notificationDTO => (
                    <NotificationCard notificationDTO={notificationDTO} />
                ))}
            </Grid>
        </InfiniteScroll>
    );
};

export default Notifications;
