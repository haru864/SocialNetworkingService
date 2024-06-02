"use client"

import React from 'react';
import CloseIcon from '@mui/icons-material/Close';
import Link from 'next/link';
import { Container, Box, Grid, Button, Typography, Avatar } from '@mui/material';
import { createTheme, ThemeProvider } from '@mui/material/styles';
import Copyright from "../../common/copyright";

const defaultTheme = createTheme();

const SessionCheckError: React.FC = () => {
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
                        Invalid Session.
                    </Typography>
                    <Grid item xs={12}>
                        <Link href="/login" passHref>
                            <Button variant="contained" color="primary" fullWidth>
                                LOGIN
                            </Button>
                        </Link>
                    </Grid>
                </Box>
                <Copyright sx={{ mt: 8, mb: 4 }} />
            </Container>
        </ThemeProvider>
    );
}

export default SessionCheckError;
