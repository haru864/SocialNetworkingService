"use client"

import * as ValidationUtil from '../utils/ValidationUtil';
import * as React from 'react';
import Avatar from '@mui/material/Avatar';
import Button from '@mui/material/Button';
import CssBaseline from '@mui/material/CssBaseline';
import TextField from '@mui/material/TextField';
import Link from '@mui/material/Link';
import Box from '@mui/material/Box';
import EmailIcon from '@mui/icons-material/Email';
import Typography from '@mui/material/Typography';
import Container from '@mui/material/Container';
import { createTheme, ThemeProvider } from '@mui/material/styles';

function Copyright(props: any) {
    return (
        <Typography variant="body2" color="text.secondary" align="center" {...props}>
            {'Copyright © '}
            <Link color="inherit" href="https://github.com/haru864">
                haru864
            </Link>{' '}
            {new Date().getFullYear()}
            {'.'}
        </Typography>
    );
}

async function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
    try {
        event.preventDefault();
        const data: FormData = new FormData(event.currentTarget);
        const username: string = data.get('username') as string;
        const email: string = data.get('email') as string;
        ValidationUtil.validateRequiredFields(username, "Username");
        ValidationUtil.validateRequiredFields(email, "Email");
        ValidationUtil.validateEmail(email);
        const msgBody = {
            username: username,
            email: email,
        };
        // const response = await fetch(`${process.env.API_DOMAIN}/api/login`, {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //     },
        //     body: JSON.stringify(msgBody),
        // });
        // console.log(response);
        // if (!response.ok) {
        //     const responseData = await response.json();
        //     throw new Error(responseData["error_message"]);
        // }
        // window.location.href = `${process.env.FRONT_DOMAIN}/home`;
    } catch (error: any) {
        console.error(error);
        alert(error);
    }
};

const defaultTheme = createTheme();

export default function SignIn() {
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
                    <Typography component="h1" variant="h5">
                        Reset Password
                    </Typography>
                    <Box component="form" onSubmit={handleSubmit} noValidate sx={{ mt: 1 }}>
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
