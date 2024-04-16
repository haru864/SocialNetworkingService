"use client"

import * as ValidationUtil from '../../utils/ValidationUtil';
import React, { useState, useEffect } from 'react';
import Avatar from '@mui/material/Avatar';
import Button from '@mui/material/Button';
import CssBaseline from '@mui/material/CssBaseline';
import TextField from '@mui/material/TextField';
import Box from '@mui/material/Box';
import EmailIcon from '@mui/icons-material/Email';
import Typography from '@mui/material/Typography';
import Container from '@mui/material/Container';
import { createTheme, ThemeProvider } from '@mui/material/styles';
import CircularProgress from '@mui/material/CircularProgress';
import Copyright from "../../common/copyright";

async function handleSubmit(event: React.FormEvent<HTMLFormElement>, setLoading: React.Dispatch<React.SetStateAction<boolean>>) {
    try {
        event.preventDefault();
        const data: FormData = new FormData(event.currentTarget);
        const username: string = data.get('username') as string;
        const email: string = data.get('email') as string;
        ValidationUtil.validateRequiredFields(username, "Username");
        ValidationUtil.validateRequiredFields(email, "Email");
        ValidationUtil.validateEmail(email);
        const msgBody = {
            'action': 'send_email',
            'username': username,
            'email': email,
        };
        setLoading(true);
        const response = await fetch(`${process.env.API_DOMAIN}/api/reset_password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(msgBody),
        });
        setLoading(false);
        console.log(response);
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        alert('A link for reconfiguration has been sent to your registered email address.');
    } catch (error: any) {
        console.error(error);
        alert(error);
    }
};

const defaultTheme = createTheme();

export default function SignIn() {
    const [loading, setLoading] = useState(false);
    if (loading) {
        return (
            <Box display="flex" justifyContent="center" alignItems="center" height="100vh">
                <CircularProgress />
            </Box>
        );
    }
    return (
        <ThemeProvider theme={defaultTheme}>
            <Container component="main" maxWidth="xs">
                <CssBaseline />
                <Box
                    sx={{
                        marginTop: 8,
                        display: 'flex',
                        flexDirection: 'column',
                        alignItems: 'center',
                    }}
                >
                    <Avatar sx={{ m: 1, bgcolor: 'info.main' }}>
                        <EmailIcon />
                    </Avatar>
                    <Typography component="h1" variant="h5" gutterBottom>
                        Reset Password
                    </Typography>
                    <Typography component="p" align='center'>
                        Link to reset your password will be sent to your registered email address.
                    </Typography>
                    <Box component="form" onSubmit={(e) => handleSubmit(e, setLoading)} noValidate sx={{ mt: 1 }}>
                        <TextField
                            margin="normal"
                            required
                            fullWidth
                            id="username"
                            label="Username"
                            name="username"
                            autoComplete=""
                            autoFocus
                        />
                        <TextField
                            margin="normal"
                            required
                            fullWidth
                            id="email"
                            label="Email"
                            name="email"
                            type="email"
                            autoComplete=""
                        />
                        <Button
                            type="submit"
                            fullWidth
                            variant="contained"
                            sx={{ mt: 3, mb: 2 }}
                        >
                            submit
                        </Button>
                    </Box>
                </Box>
                <Copyright sx={{ mt: 8, mb: 4 }} />
            </Container>
        </ThemeProvider>
    );
}
