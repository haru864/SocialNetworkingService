'use client'

import React from 'react';
import CommonLayout from '../common/CommonLayout';
import Notifications from './components/Notifications';

const HomePage = () => {
    return (
        <CommonLayout>
            <Notifications />
        </CommonLayout >
    );
};

export default HomePage;
