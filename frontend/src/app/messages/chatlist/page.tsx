'use client'

import React from 'react';
import CommonLayout from '../../common/CommonLayout';
import ChatList from './components/ChatList';

const HomePage = () => {
    return (
        <CommonLayout>
            <ChatList />
        </CommonLayout >
    );
};

export default HomePage;
