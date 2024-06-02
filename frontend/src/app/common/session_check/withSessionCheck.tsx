import React from 'react';
import { Box, CircularProgress } from '@mui/material';
import useSessionCheck from './useSessionCheck';

const withSessionCheck = (WrappedComponent: React.ComponentType) => {
    return (props: any) => {
        const { isLoading, isAuthenticated } = useSessionCheck();

        if (isLoading) {
            return (
                <Box display="flex" justifyContent="center" alignItems="center" height="100vh">
                    <CircularProgress />
                </Box>
            );
        }

        if (!isAuthenticated) {
            return null;
        }

        return <WrappedComponent {...props} />;
    };
};

export default withSessionCheck;
