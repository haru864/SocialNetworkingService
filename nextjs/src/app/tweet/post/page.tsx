'use client'

import React from 'react';
import CommonLayout from '../../common/CommonLayout';
import TweetInput from './components/TweetInput';
import withSessionCheck from '@/app/common/session_check/withSessionCheck';

const TweetPage = () => {
    return (
        <CommonLayout>
            <TweetInput />
        </CommonLayout >
    );
};

export default withSessionCheck(TweetPage);
