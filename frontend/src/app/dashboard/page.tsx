'use client'

import React from 'react';
import CommonLayout from '../common/CommonLayout';
import UserProfile from './components/UserProfile';
import withSessionCheck from '../common/session_check/withSessionCheck';

const DashBoard = () => {
    return (
        <CommonLayout>
            <UserProfile />
        </CommonLayout >
    );
};

export default withSessionCheck(DashBoard);
