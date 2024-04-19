"use client"

import React, { useState, useEffect, Suspense } from 'react';
import VerifiedUserIcon from '@mui/icons-material/VerifiedUser';
import CloseIcon from '@mui/icons-material/Close';
import Link from 'next/link';
import { Container, Box, Grid, Button, Typography, Avatar, CircularProgress } from '@mui/material';
import { createTheme, ThemeProvider } from '@mui/material/styles';
import { useSearchParams } from "next/navigation";
import Copyright from "../common/copyright";

async function validateEmail(
    setvalidating: React.Dispatch<React.SetStateAction<boolean>>,
    setIsCertified: React.Dispatch<React.SetStateAction<boolean>>,
    hash: string | null) {
    try {
        if (hash === null) {
            throw new Error('Invalid URL, access from the link attached to the email.');
        }
        const response = await fetch(`${process.env.API_DOMAIN}/api/validate_email?id=${hash}`, {
            method: 'GET'
        });
        setvalidating(false);
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        setIsCertified(true);
    } catch (error: any) {
        setIsCertified(false);
        console.error(error);
        alert(error);
    }
};

const defaultTheme = createTheme();

function SignUp() {
    const [validating, setvalidating] = useState(true);
    const [isCertified, setIsCertified] = useState(false);
    const searchParams = useSearchParams();
    const id = searchParams.get("id");
    useEffect(() => {
        validateEmail(setvalidating, setIsCertified, id);
    }, []);
    if (validating) {
        return (
            <Box display="flex" justifyContent="center" alignItems="center" height="100vh">
                <CircularProgress />
            </Box>
        );
    } else if (!isCertified) {
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
                            Sign-up failed.
                        </Typography>
                        <Grid item xs={12}>
                            <Button variant="contained" color="primary" fullWidth>
                                <Link href="/signup" passHref>
                                    <Typography component="a">SIGN-UP</Typography>
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
                            Sign-up completed.
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
    }
}

export default function WrappedComponent() {
    return (
        <Suspense fallback={<div>Loading...</div>}>
            <SignUp />
        </Suspense>
    );
}
