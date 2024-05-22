import { Follow } from "./Follow";
import { Like } from "./Like";
import { NotificationDTO } from "./NotificationDTO";
import { Message } from "@/app/messages/chatlist/components/Chat";

export async function getNotifications(page: number): Promise<NotificationDTO[]> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/notifications?page=${page}&limit=20`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let notificationDTOs: NotificationDTO[] = [];
        if (jsonData !== null) {
            const notifications = jsonData['notifications'];
            for (const notificationData of notifications) {
                const notificationDTO = new NotificationDTO(notificationData);
                notificationDTOs.push(notificationDTO);
            }
        }
        return notificationDTOs;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

export async function getLikeDataByLikeId(likeId: number): Promise<Like> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/likes?like_id=${likeId}`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        const likeData = jsonData['like_data'];
        const likeObj = new Like(likeData);
        return likeObj;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

export async function getMessageData(messageId: number): Promise<Message> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/messages?message_id=${messageId}`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        const messageData = jsonData['message'];
        const message = new Message(messageData);
        return message;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}

export async function getFollowData(followId: number): Promise<Follow> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/follows/follow?follow_id=${followId}`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        const followData = jsonData['follow'];
        const follow = new Follow(followData);
        return follow;
    } catch (error: any) {
        console.error(error);
        throw error;
    }
}
