'use client'

import React from 'react';
import ProfileForm from './components/ProfileForm';
import CommonLayout from '../common/CommonLayout';
import withSessionCheck from '../common/session_check/withSessionCheck';

const ProfilePage = () => {
    return (
        <CommonLayout>
            <ProfileForm />
        </CommonLayout >
    );
};

export default withSessionCheck(ProfilePage);
