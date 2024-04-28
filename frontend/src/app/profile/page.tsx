'use client'

import React from 'react';
import ProfileForm from './components/ProfileForm';
import CommonLayout from '../common/CommonLayout';

const HomePage = () => {
    return (
        <CommonLayout>
            <ProfileForm />
        </CommonLayout >
    );
};

export default HomePage;
