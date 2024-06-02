"use client"

import React from 'react';
import ContentArea from './components/ContentArea';
import CommonLayout from '../common/CommonLayout';
import withSessionCheck from '../common/session_check/withSessionCheck';

const HomePage = () => {
    return (
        <CommonLayout>
            <ContentArea />
        </CommonLayout >
    );
};

export default withSessionCheck(HomePage);
