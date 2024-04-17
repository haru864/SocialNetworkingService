"use client"

import * as ValidationUtil from '../utils/ValidationUtil';
import React, { useState, useEffect } from 'react';
import Avatar from '@mui/material/Avatar';
import Button from '@mui/material/Button';
import CssBaseline from '@mui/material/CssBaseline';
import TextField from '@mui/material/TextField';
import Link from '@mui/material/Link';
import Grid from '@mui/material/Grid';
import Box from '@mui/material/Box';
import LockOutlinedIcon from '@mui/icons-material/LockOutlined';
import Typography from '@mui/material/Typography';
import Container from '@mui/material/Container';
import { createTheme, ThemeProvider } from '@mui/material/styles';
import CircularProgress from '@mui/material/CircularProgress';
import Copyright from "../common/copyright";

async function handleSubmit(
    event: React.FormEvent<HTMLFormElement>,
    setLoading: React.Dispatch<React.SetStateAction<boolean>>
) {
    try {
        event.preventDefault();
        const data: FormData = new FormData(event.currentTarget);
        const username: string = data.get('username') as string;
        const password: string = data.get('password') as string;
        const passwordConf: string = data.get('password_confirmation') as string;
        const email: string = data.get('email') as string;
        const selfIntro: string = data.get('self_introduction') as string;
        const country: string = data.get('country') as string;
        const state: string = data.get('state') as string;
        const city: string = data.get('city') as string;
        const town: string = data.get('town') as string;
        const hobby_1: string = data.get('hobby_1') as string;
        const hobby_2: string = data.get('hobby_2') as string;
        const hobby_3: string = data.get('hobby_3') as string;
        const career_1: string = data.get('career_1') as string;
        const career_2: string = data.get('career_2') as string;
        const career_3: string = data.get('career_3') as string;
        ValidationUtil.validateRequiredFields(username, "Username");
        ValidationUtil.validateRequiredFields(password, "Password");
        ValidationUtil.validateRequiredFields(password, "passwordConf");
        ValidationUtil.validateRequiredFields(password, "email");
        ValidationUtil.validateUsername(username);
        if (password !== passwordConf) {
            throw new Error('Confirmation password does not match the other.');
        }
        ValidationUtil.validatePassword(password);
        ValidationUtil.validateEmail(email);
        ValidationUtil.validateCharCount(email, "Email", null, 100);
        ValidationUtil.validateCharCount(selfIntro, "Self Introduction", null, 50);
        ValidationUtil.validateCharCount(country, "Country", null, 100);
        ValidationUtil.validateCharCount(state, "State", null, 100);
        ValidationUtil.validateCharCount(city, "City", null, 100);
        ValidationUtil.validateCharCount(town, "Town", null, 100);
        ValidationUtil.validateCharCount(hobby_1, "Hobby_1", null, 100);
        ValidationUtil.validateCharCount(hobby_2, "Hobby_2", null, 100);
        ValidationUtil.validateCharCount(hobby_3, "Hobby_3", null, 100);
        ValidationUtil.validateCharCount(career_1, "Career_1", null, 100);
        ValidationUtil.validateCharCount(career_2, "Career_2", null, 100);
        ValidationUtil.validateCharCount(career_3, "Career_3", null, 100);
        setLoading(true);
        const response = await fetch(`${process.env.API_DOMAIN}/api/signup`, {
            method: 'POST',
            body: data
        });
        setLoading(false);
        if (!response.ok) {
            const responseData = await response.json();
            // console.log(responseData);
            throw new Error(responseData["error_message"]);
        }
        alert('URL for authentication has been sent to the email address you entered.\nPlease follow the instructions in the e-mail to complete sign-up.');
    } catch (error: any) {
        console.error(error);
        alert(error);
    }
};

const defaultTheme = createTheme();

export default function SignUp() {
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
                    <Avatar sx={{ m: 1, bgcolor: 'secondary.main' }}>
                        <LockOutlinedIcon />
                    </Avatar>
                    <Typography component="h1" variant="h5">
                        Sign up
                    </Typography>
                    <Box component="form" noValidate onSubmit={(e) => handleSubmit(e, setLoading)} sx={{ mt: 3 }}>
                        <Grid container spacing={2}>
                            <Grid item xs={12}>
                                <Typography component="p" align='center'>
                                    Username - Up to 15 characters, single-byte alphabetic characters and numbers are available.
                                </Typography>
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    required
                                    fullWidth
                                    id="username"
                                    label="Username"
                                    name="username"
                                    autoComplete="username"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <Typography component="p" align='center'>
                                    Password - At least 8 characters, contain single-byte lowercase and uppercase alphabetic characters, numbers and symbols.
                                </Typography>
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    required
                                    fullWidth
                                    id="password"
                                    label="Password"
                                    name="password"
                                    autoComplete="password"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    required
                                    fullWidth
                                    id="password_confirmation"
                                    label="Password Confirmation"
                                    name="password_confirmation"
                                    autoComplete="password_confirmation"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <Typography component="p" align='center'>
                                    Email - Used to reset passwords and for notifications from the service.
                                </Typography>
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    required
                                    fullWidth
                                    id="email"
                                    label="Email Address"
                                    name="email"
                                    autoComplete="email"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <Typography component="p" align='center'>
                                    Self Introduction - Up to 50 characters
                                </Typography>
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    name="self_introduction"
                                    label="Self Introduction"
                                    type="self_introduction"
                                    id="self_introduction"
                                    autoComplete="self_introduction"
                                    multiline
                                    minRows={3}
                                    maxRows={5}
                                />
                            </Grid>
                            <Grid item xs={12} style={{ display: 'flex', justifyContent: 'center' }}>
                                <input
                                    accept="image/jpeg, image/png, image/gif"
                                    type="file"
                                    id="upload_file_button"
                                    name="profile_image"
                                    style={{ display: 'none' }}
                                />
                                <label htmlFor="upload_file_button">
                                    <Button variant="contained" component="span">
                                        Upload Profile Image
                                    </Button>
                                </label>
                            </Grid>
                            <Grid item xs={12}>
                                <Typography component="p" align='center'>
                                    Address - Will be open to other users.
                                </Typography>
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    id="country"
                                    label="Country"
                                    name="country"
                                    autoComplete="country"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    id="state"
                                    label="State"
                                    name="state"
                                    autoComplete="state"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    id="city"
                                    label="City"
                                    name="city"
                                    autoComplete="city"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    id="town"
                                    label="Town"
                                    name="town"
                                    autoComplete="town"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <Typography component="p" align='center'>
                                    Hobbies - Connect with your hobbies.
                                </Typography>
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    id="hobby_1"
                                    label="Hobby 1"
                                    name="hobby_1"
                                    autoComplete="hobby_1"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    id="hobby_2"
                                    label="Hobby 2"
                                    name="hobby_2"
                                    autoComplete="hobby_2"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    id="hobby_3"
                                    label="Hobby 3"
                                    name="hobby_3"
                                    autoComplete="hobby_3"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <Typography component="p" align='center'>
                                    Careers - Connect with your jobs.
                                </Typography>
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    id="career_1"
                                    label="Career 1"
                                    name="career_1"
                                    autoComplete="career_1"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    id="career_2"
                                    label="Career 2"
                                    name="career_2"
                                    autoComplete="career_2"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    id="career_3"
                                    label="Career 3"
                                    name="career_3"
                                    autoComplete="career_3"
                                />
                            </Grid>
                        </Grid>
                        <Button
                            type="submit"
                            fullWidth
                            variant="contained"
                            sx={{ mt: 3, mb: 2 }}
                        >
                            Sign Up
                        </Button>
                        <Grid container justifyContent="flex-end">
                            <Grid item>
                                <Link href="/login" variant="body2">
                                    Already have an account? Log-in
                                </Link>
                            </Grid>
                        </Grid>
                    </Box>
                </Box>
                <Copyright sx={{ mt: 5 }} />
            </Container>
        </ThemeProvider >
    );
}
