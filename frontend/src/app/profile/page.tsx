'use client'

import React from 'react';
import UserForm from './components/UserForm';
import CommonLayout from '../common/CommonLayout';

const HomePage = () => {
    return (
        <CommonLayout>
            <UserForm />
        </CommonLayout >
    );
};

export default HomePage;
