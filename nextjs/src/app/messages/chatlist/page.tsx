'use client'

import React from 'react';
import CommonLayout from '../../common/CommonLayout';
import ChatList from './components/ChatList';
import withSessionCheck from '@/app/common/session_check/withSessionCheck';

const Chats = () => {
    return (
        <CommonLayout>
            <ChatList />
        </CommonLayout >
    );
};

export default withSessionCheck(Chats);
