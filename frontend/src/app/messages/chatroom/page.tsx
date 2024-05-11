'use client'

import React from 'react';
import CommonLayout from '../../common/CommonLayout';
import ChatHistory from './components/ChatHistory';

const HomePage = () => {
    return (
        <CommonLayout>
            <ChatHistory />
        </CommonLayout >
    );
};

export default HomePage;
