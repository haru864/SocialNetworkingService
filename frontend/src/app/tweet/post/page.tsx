'use client'

import React from 'react';
import CommonLayout from '../../common/CommonLayout';
import TweetInput from './components/TweetInput';

const HomePage = () => {
    return (
        <CommonLayout>
            <TweetInput />
        </CommonLayout >
    );
};

export default HomePage;
