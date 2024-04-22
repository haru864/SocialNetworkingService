import React, { useState, useEffect } from 'react';
import { Button, TextField, Box, Typography } from '@mui/material';
import { UserData } from './UserData';
import Grid from '@mui/material/Grid';
import Link from 'next/link';
import CircularProgress from '@mui/material/CircularProgress';
import * as ValidationUtil from '../../utils/ValidationUtil';

async function getUserData(): Promise<UserData | null> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/profile`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        const jsonData = await response.json();
        let profile = null;
        if (jsonData !== null) {
            profile = jsonData['profile'];
        }
        return new UserData(profile);
    } catch (error: any) {
        console.error(error);
        alert(error);
        return null;
    }
}

// TODO Reactエラーが出るので直す
async function updateProfile(
    event: React.FormEvent<HTMLFormElement>,
    setLoading: React.Dispatch<React.SetStateAction<boolean>>
): Promise<void> {
    try {
        event.preventDefault();
        const data: FormData = new FormData(event.currentTarget);
        data.append("action", "edit");
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
        const response = await fetch(`${process.env.API_DOMAIN}/api/profile`, {
            method: 'POST',
            body: data
        });
        setLoading(false);
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        alert('URL for authentication has been sent to the email address you entered.');
    } catch (error: any) {
        console.error(error);
        alert(error);
    }
}

const UserForm: React.FC = () => {
    const [userData, setUserData] = useState<UserData | null>(null);
    const [loading, setLoading] = useState(false);
    if (loading) {
        return (
            <Box display="flex" justifyContent="center" alignItems="center" height="100vh">
                <CircularProgress />
            </Box>
        );
    }
    useEffect(() => {
        (async () => {
            const userData = await getUserData();
            setUserData(userData);
        })();
    }, []);
    if (!userData) {
        return <div>Loading...</div>;
    }
    const profileImageUrl = `${process.env.PROFILE_IMAGE_THUMBNAIL_URL}/${userData.profileImage}`;
    const profileImageOriginalUrl = `${process.env.PROFILE_IMAGE_UPLOAD_URL}/${userData.profileImage}`;
    const handleChange = (prop: keyof UserData) => (event: React.ChangeEvent<HTMLInputElement>) => {
        if (userData) {
            setUserData({ ...userData, [prop]: event.target.value });
        }
    };
    return (
        <Box
            sx={{ display: 'flex', flexDirection: 'column', gap: 2, maxWidth: 400, m: 'auto' }}
            component="form"
            noValidate
            onSubmit={(e) => updateProfile(e, setLoading)}
        >
            <Typography variant="h6">User Profile</Typography>
            <TextField name="username" label="Username" value={userData.username} onChange={handleChange('username')} />
            <TextField name="password" label="Password" />
            <TextField name="password_confirmation" label="Password Confirmation" />
            <TextField name="email" label="Email" type="email" value={userData.email} onChange={handleChange('email')} />
            <TextField name="self_introduction" label="Self Introduction" multiline minRows={3} maxRows={5} value={userData.selfIntroduction} />
            <Box display="flex" alignItems="center">
                <Typography>Profile Image:</Typography>
                <Link href={profileImageOriginalUrl}>
                    <img src={profileImageUrl} alt="Profile" />
                </Link>
            </Box>
            <Grid item xs={12} style={{ display: 'flex', justifyContent: 'center' }}>
                <input
                    accept="image/jpeg, image/png, image/gif"
                    type="file"
                    name="profile_image"
                    style={{ display: 'none' }}
                />
                <label htmlFor="upload_file_button">
                    <Button variant="contained" component="span">
                        Upload New Profile Image
                    </Button>
                </label>
            </Grid>
            <TextField name="country" label="Country" value={userData.country} onChange={handleChange('country')} />
            <TextField name="state" label="State" value={userData.state} onChange={handleChange('state')} />
            <TextField name="city" label="City" value={userData.city} onChange={handleChange('city')} />
            <TextField name="town" label="Town" value={userData.town} onChange={handleChange('town')} />
            <TextField name="hobby_1" label="Hobby 1" value={userData.hobby_1} onChange={handleChange('hobby_1')} />
            <TextField name="hobby_2" label="Hobby 2" value={userData.hobby_2} onChange={handleChange('hobby_2')} />
            <TextField name="hobby_3" label="Hobby 3" value={userData.hobby_3} onChange={handleChange('hobby_3')} />
            <TextField name="career_1" label="Career 1" value={userData.career_1} onChange={handleChange('career_1')} />
            <TextField name="career_2" label="Career 2" value={userData.career_2} onChange={handleChange('career_2')} />
            <TextField name="career_3" label="Career 3" value={userData.career_3} onChange={handleChange('career_3')} />
            <Button type="submit" variant="contained" color="primary">Update Profile</Button>
            <Button variant="contained" color="error">Delete Account</Button>
        </Box >
    );
}

export default UserForm;
