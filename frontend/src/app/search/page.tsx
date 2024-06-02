'use client'

import React from 'react';
import CommonLayout from '../common/CommonLayout';
import SearchResult from './components/SearchResult';
import withSessionCheck from '../common/session_check/withSessionCheck';

const SearchResultPage = () => {
    return (
        <CommonLayout>
            <SearchResult />
        </CommonLayout >
    );
};

export default withSessionCheck(SearchResultPage);
