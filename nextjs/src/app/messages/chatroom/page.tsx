'use client'

import React from 'react';
import CommonLayout from '../../common/CommonLayout';
import ChatHistory from './components/ChatHistory';
import withSessionCheck from '@/app/common/session_check/withSessionCheck';

const ChatRoom = () => {
    return (
        <CommonLayout>
            <ChatHistory />
        </CommonLayout >
    );
};

export default withSessionCheck(ChatRoom);
