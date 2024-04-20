import React from 'react';
import Sidebar from './components/Sidebar';
import ContentArea from './components/ContentArea';
import SearchAppBar from '../common/appbar';
import CommonLayout from '../common/CommonLayout';

const HomePage = () => {
    return (
        // <div>
        //     <SearchAppBar />
        //     <Sidebar />
        //     <ContentArea />
        // </div>
        <CommonLayout>
            <ContentArea />
        </CommonLayout >
    );
};

export default HomePage;
