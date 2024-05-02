'use client'

import React from 'react';
import CommonLayout from '../common/CommonLayout';
import UserProfile from './components/UserProfile';

const HomePage = () => {
    return (
        <CommonLayout>
            <UserProfile />
        </CommonLayout >
    );
};

export default HomePage;
