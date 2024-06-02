'use client'

import React from 'react';
import CommonLayout from '../common/CommonLayout';
import Notifications from './components/Notifications';
import withSessionCheck from '../common/session_check/withSessionCheck';

const NotificationList = () => {
    return (
        <CommonLayout>
            <Notifications />
        </CommonLayout >
    );
};

export default withSessionCheck(NotificationList);
