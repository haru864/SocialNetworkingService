'use client'

import React from 'react';
import CommonLayout from '../../common/CommonLayout';
import ReplyList from './components/ReplyList';
import withSessionCheck from '@/app/common/session_check/withSessionCheck';

const ReplyListPage = () => {
    return (
        <CommonLayout>
            <ReplyList />
        </CommonLayout >
    );
};

export default withSessionCheck(ReplyListPage);
