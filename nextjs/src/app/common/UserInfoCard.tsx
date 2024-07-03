import React from 'react';
import {
    Grid,
    Card,
    CardContent,
    Typography,
    CardMedia,
    CardActionArea
} from '@mui/material';
import Link from 'next/link';
import { UserInfo } from './UserInfo';

interface UserInfoCardProps {
    userInfo: UserInfo;
}

const UserInfoCard: React.FC<UserInfoCardProps> = ({ userInfo }) => {
    return (
        <Grid item key={userInfo.username} xs={12} sm={6} md={4}>
            <Link href={`/userinfo?id=${userInfo.id}`}>
                <Card sx={{ maxWidth: 450 }}>
                    <CardActionArea>
                        <Link href={userInfo.getUploadedImageUrl()}>
                            <CardMedia
                                component="img"
                                image={userInfo.getThumbnailUrl()}
                                alt="profile image"
                                sx={{
                                    height: 140,
                                    width: 1,
                                    objectFit: 'contain'
                                }}
                            />
                        </Link>
                        <CardContent>
                            <Typography gutterBottom variant="h5" component="div">
                                {userInfo.username}
                            </Typography>
                            <Typography variant="body2" color="text.secondary">
                                {userInfo.selfIntroduction}
                            </Typography>
                            <Typography variant="body2" color="text.secondary">
                                Country: {userInfo.country}, State: {userInfo.state}, City: {userInfo.city}, Town: {userInfo.town}
                            </Typography>
                            <Typography variant="body2" color="text.secondary">
                                Hobbies: {userInfo.hobby_1}, {userInfo.hobby_2}, {userInfo.hobby_3}
                            </Typography>
                            <Typography variant="body2" color="text.secondary">
                                Careers: {userInfo.career_1}, {userInfo.career_2}, {userInfo.career_3}
                            </Typography>
                        </CardContent>
                    </CardActionArea>
                </Card>
            </Link>
        </Grid>
    );
};

export default UserInfoCard;
