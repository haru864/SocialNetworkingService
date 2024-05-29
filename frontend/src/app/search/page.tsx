'use client'

import React from 'react';
import CommonLayout from '../common/CommonLayout';
import SearchResult from './components/SearchResult';

const HomePage = () => {
    return (
        <CommonLayout>
            <SearchResult />
        </CommonLayout >
    );
};

export default HomePage;
