"use client"

import * as ValidationUtil from '../../utils/ValidationUtil';
import React, { useState, useEffect } from 'react';
import Avatar from '@mui/material/Avatar';
import Button from '@mui/material/Button';
import CssBaseline from '@mui/material/CssBaseline';
import TextField from '@mui/material/TextField';
import Box from '@mui/material/Box';
import PasswordIcon from '@mui/icons-material/Password';
import Typography from '@mui/material/Typography';
import Container from '@mui/material/Container';
import { createTheme, ThemeProvider } from '@mui/material/styles';
import CircularProgress from '@mui/material/CircularProgress';
import { useSearchParams } from "next/navigation";
import Copyright from "../../common/copyright";

async function handleSubmit(
    event: React.FormEvent<HTMLFormElement>,
    setLoading: React.Dispatch<React.SetStateAction<boolean>>,
    hash: string | null
) {
    try {
        event.preventDefault();
        const data: FormData = new FormData(event.currentTarget);
        const newPassword: string = data.get('new_password') as string;
        const newPasswordConfirmation: string = data.get('new_password_confirmation') as string;
        ValidationUtil.validateRequiredFields(newPassword, "New Password");
        ValidationUtil.validateRequiredFields(newPasswordConfirmation, "New Password Confirmation");
        ValidationUtil.validateRequiredFields(hash, "URL-id");
        if (newPassword !== newPasswordConfirmation) {
            throw new Error('Confirmation password does not match the other.');
        }
        ValidationUtil.validatePassword(newPassword);
        const msgBody = {
            'action': 'reset_password',
            'new_password': newPassword,
            'hash': hash,
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
        alert(' Successfully reset password.');
        window.location.href = `${process.env.FRONT_DOMAIN}/login`;
    } catch (error: any) {
        console.error(error);
        alert(error);
    }
};

const defaultTheme = createTheme();

export default function SignIn() {
    const [loading, setLoading] = useState(false);
    const searchParams = useSearchParams();
    const id = searchParams.get("id");
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
                        <PasswordIcon />
                    </Avatar>
                    <Typography component="h1" variant="h5" gutterBottom>
                        Reset Password
                    </Typography>
                    <Typography component="p" align='center'>
                        At least 8 characters, contain single-byte lowercase and uppercase alphabetic characters, numbers and symbols.
                    </Typography>
                    <Box component="form" onSubmit={(e) => handleSubmit(e, setLoading, id)} noValidate sx={{ mt: 1 }}>
                        <TextField
                            margin="normal"
                            required
                            fullWidth
                            id="new_password"
                            label="New Password"
                            name="new_password"
                            autoComplete=""
                            autoFocus
                        />
                        <TextField
                            margin="normal"
                            required
                            fullWidth
                            id="new_password_confirmation"
                            label="New Password Confirmation"
                            name="new_password_confirmation"
                            type="new_password_confirmation"
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
