import React, { useState, useEffect } from 'react';
import { Button, TextField, Box, Typography } from '@mui/material';
import { ProfileInfo } from './ProfileInfo';
import Grid from '@mui/material/Grid';
import Link from 'next/link';
import CircularProgress from '@mui/material/CircularProgress';
import { deleteAccount, getUserData, updateProfile } from './ProfileFormFunctions';

const ProfileForm: React.FC = () => {
    const [profileInfo, setProfileInfo] = useState<ProfileInfo | null>(null);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        (async () => {
            const userData = await getUserData();
            setProfileInfo(userData);
        })();
    }, []);

    if (loading) {
        return (
            <Box display="flex" justifyContent="center" alignItems="center" height="100vh">
                <CircularProgress />
            </Box>
        );
    }

    if (!profileInfo) {
        return <div>Loading...</div>;
    }

    const profileImageUrl = `${process.env.PROFILE_IMAGE_THUMBNAIL_URL}/${profileInfo.profileImage}`;
    const profileImageOriginalUrl = `${process.env.PROFILE_IMAGE_UPLOAD_URL}/${profileInfo.profileImage}`;

    const handleChange = (prop: keyof ProfileInfo) => (event: React.ChangeEvent<HTMLInputElement>) => {
        if (profileInfo) {
            setProfileInfo({ ...profileInfo, [prop]: event.target.value });
        }
    };

    const handleDeleteAccount = () => {
        try {
            deleteAccount();
            alert('Deleted account successfully');
            window.location.href = `/login`;
        } catch (error) {
            console.error(error);
            alert('Failed to delete account');
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
            <TextField name="username" label="Username" value={profileInfo.username} onChange={handleChange('username')} />
            <TextField name="password" label="Password" />
            <TextField name="password_confirmation" label="Password Confirmation" />
            <TextField name="email" label="Email" type="email" value={profileInfo.email} onChange={handleChange('email')} />
            <TextField name="self_introduction" label="Self Introduction" multiline minRows={3} maxRows={5} value={profileInfo.selfIntroduction} onChange={handleChange('selfIntroduction')} />
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
                    id="upload_file_button"
                    name="profile_image"
                    style={{ display: 'none' }}
                />
                <label htmlFor="upload_file_button">
                    <Button variant="contained" component="span">
                        Upload New Profile Image
                    </Button>
                </label>
            </Grid>
            <TextField name="country" label="Country" value={profileInfo.country} onChange={handleChange('country')} />
            <TextField name="state" label="State" value={profileInfo.state} onChange={handleChange('state')} />
            <TextField name="city" label="City" value={profileInfo.city} onChange={handleChange('city')} />
            <TextField name="town" label="Town" value={profileInfo.town} onChange={handleChange('town')} />
            <TextField name="hobby_1" label="Hobby 1" value={profileInfo.hobby_1} onChange={handleChange('hobby_1')} />
            <TextField name="hobby_2" label="Hobby 2" value={profileInfo.hobby_2} onChange={handleChange('hobby_2')} />
            <TextField name="hobby_3" label="Hobby 3" value={profileInfo.hobby_3} onChange={handleChange('hobby_3')} />
            <TextField name="career_1" label="Career 1" value={profileInfo.career_1} onChange={handleChange('career_1')} />
            <TextField name="career_2" label="Career 2" value={profileInfo.career_2} onChange={handleChange('career_2')} />
            <TextField name="career_3" label="Career 3" value={profileInfo.career_3} onChange={handleChange('career_3')} />
            <Button type="submit" variant="contained" color="primary">Update Profile</Button>
            <Button variant="contained" color="error" onClick={handleDeleteAccount}>Delete Account</Button>
        </Box >
    );
}

export default ProfileForm;
