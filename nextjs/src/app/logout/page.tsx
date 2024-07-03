"use client"

import React, { useState, useEffect, Suspense } from 'react';
import VerifiedUserIcon from '@mui/icons-material/VerifiedUser';
import CloseIcon from '@mui/icons-material/Close';
import Link from 'next/link';
import { Container, Box, Grid, Button, Typography, Avatar, CircularProgress } from '@mui/material';
import { createTheme, ThemeProvider } from '@mui/material/styles';
import Copyright from "@/app/common/copyright";

async function executeLogoutApi(
    setIsLoading: React.Dispatch<React.SetStateAction<boolean>>,
    setIsSuccess: React.Dispatch<React.SetStateAction<boolean>>
) {
    try {
        setIsLoading(true);
        const response = await fetch(`${process.env.API_DOMAIN}/api/logout`, {
            method: 'POST'
        });
        setIsLoading(false);
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        setIsSuccess(true);
    } catch (error: any) {
        setIsSuccess(false);
        console.error(error);
        alert(error);
    }
};

const defaultTheme = createTheme();

function Logout() {
    const [isLoading, setIsLoading] = useState(true);
    const [isSuccess, setIsSuccess] = useState(false);

    useEffect(() => {
        executeLogoutApi(setIsLoading, setIsSuccess);
    }, []);

    if (isLoading) {
        return (
            <Box display="flex" justifyContent="center" alignItems="center" height="100vh">
                <CircularProgress />
            </Box>
        );
    } else if (!isSuccess) {
        return (
            <ThemeProvider theme={defaultTheme}>
                <Container component="main" maxWidth="xs">
                    <Box
                        sx={{
                            marginTop: 8,
                            display: 'flex',
                            flexDirection: 'column',
                            alignItems: 'center',
                        }}
                    >
                        <Avatar sx={{ m: 1, bgcolor: 'error.main' }}>
                            <CloseIcon />
                        </Avatar>
                        <Typography component="h1" variant="h5" gutterBottom>
                            Logout failed.
                        </Typography>
                        <Grid item xs={12}>
                            <Button variant="contained" color="primary" fullWidth>
                                <Link href="/home" passHref>
                                    <Typography component="a">HOME</Typography>
                                </Link>
                            </Button>
                        </Grid>
                    </Box>
                    <Copyright sx={{ mt: 8, mb: 4 }} />
                </Container>
            </ThemeProvider>
        );
    } else {
        return (
            <ThemeProvider theme={defaultTheme}>
                <Container component="main" maxWidth="xs">
                    <Box
                        sx={{
                            marginTop: 8,
                            display: 'flex',
                            flexDirection: 'column',
                            alignItems: 'center',
                        }}
                    >
                        <Avatar sx={{ m: 1, bgcolor: 'secondary.main' }}>
                            <VerifiedUserIcon />
                        </Avatar>
                        <Typography component="h1" variant="h5" gutterBottom>
                            Logout completed.
                        </Typography>
                        <Grid item xs={12}>
                            <Button variant="contained" color="primary" fullWidth>
                                <Link href="/login" passHref>
                                    <Typography component="a">LOGIN</Typography>
                                </Link>
                            </Button>
                        </Grid>
                    </Box>
                    <Copyright sx={{ mt: 8, mb: 4 }} />
                </Container>
            </ThemeProvider>
        );
    }
}

export default function WrappedComponent() {
    return (
        <Suspense fallback={<div>Loading...</div>}>
            <Logout />
        </Suspense>
    );
}
