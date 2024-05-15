import { ChatInfo, ChatUser, Message } from './ChatInfo';
import * as ValidationUtil from '../../../utils/ValidationUtil';

export async function getChatInfo(chatPartnerId: number, page: number): Promise<ChatInfo> {
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

export async function handleSendMessage(
    event: React.FormEvent<HTMLFormElement>,
    setLoading: React.Dispatch<React.SetStateAction<boolean>>,
    chatPartnerId: number
) {
    try {
        event.preventDefault();
        const data: FormData = new FormData(event.currentTarget);
        const message: string = data.get('message') as string;
        ValidationUtil.validateCharCount(message, "Message", 1, 200);
        setLoading(true);
        const response = await fetch(`${process.env.API_DOMAIN}/api/messages/${chatPartnerId}`, {
            method: 'POST',
            body: data
        });
        setLoading(false);
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
    } catch (error: any) {
        console.error(error);
        alert(error);
    }
}
