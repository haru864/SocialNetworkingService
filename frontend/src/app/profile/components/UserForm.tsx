import React, { useState, useEffect } from 'react';
import { Button, TextField, Box, Typography } from '@mui/material';
import { UserData } from './UserData';


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

const UserForm: React.FC = () => {
    const [userData, setUserData] = useState<UserData | null>(null);

    useEffect(() => {
        (async () => {
            const userData = await getUserData();
            setUserData(userData);
        })();
    }, []);

    const handleChange = (prop: keyof UserData) => (event: React.ChangeEvent<HTMLInputElement>) => {
        if (userData) {
            setUserData({ ...userData, [prop]: event.target.value });
        }
    };

    if (!userData) {
        return <div>Loading...</div>;
    }

    return (
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2, maxWidth: 400, m: 'auto' }}>
            <Typography variant="h6">User Profile</Typography>
            <TextField label="Username" value={userData.username} onChange={handleChange('username')} />
            <TextField label="Email" type="email" value={userData.email} onChange={handleChange('email')} />
            <TextField label="Self Introduction" multiline rows={4} value={userData.selfIntroduction} onChange={handleChange('selfIntroduction')} />
            <Typography>Profile Image: {userData.profileImage}</Typography>
            <TextField label="Country" value={userData.country} onChange={handleChange('country')} />
            <TextField label="State" value={userData.state} onChange={handleChange('state')} />
            <TextField label="City" value={userData.city} onChange={handleChange('city')} />
            <TextField label="Town" value={userData.town} onChange={handleChange('town')} />
            {userData.hobbies.map((hobby, index) => (
                <TextField key={index} label={`Hobby ${index + 1}`} value={hobby} onChange={(e) => {
                    let newHobbies = [...userData.hobbies];
                    newHobbies[index] = e.target.value;
                    setUserData({ ...userData, hobbies: newHobbies });
                }} />
            ))}
            {userData.careers.map((career, index) => (
                <TextField key={index} label={`Career ${index + 1}`} value={career} onChange={(e) => {
                    let newCareers = [...userData.careers];
                    newCareers[index] = e.target.value;
                    setUserData({ ...userData, careers: newCareers });
                }} />
            ))}
            <Button variant="contained" color="primary">Update Profile</Button>
            <Button variant="outlined" color="secondary">Cancel</Button>
        </Box>
    );
}

export default UserForm;
