import React from 'react';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemAvatar from '@mui/material/ListItemAvatar';
import Avatar from '@mui/material/Avatar';
import ListItemText from '@mui/material/ListItemText';
import Typography from '@mui/material/Typography';

const followersData = [
    { name: "John Doe", handle: "@johndoe", avatar: "/path/to/avatar1.jpg" },
    { name: "Jane Smith", handle: "@janesmith", avatar: "/path/to/avatar2.jpg" },
    { name: "Alice Johnson", handle: "@alicejohn", avatar: "/path/to/avatar3.jpg" }
];

const Followers = () => {
    return (
        <div>
            <Typography variant="h5" gutterBottom>
                Your Followers
            </Typography>
            <List>
                {followersData.map((follower, index) => (
                    <ListItem key={index}>
                        <ListItemAvatar>
                            <Avatar src={follower.avatar} />
                        </ListItemAvatar>
                        <ListItemText primary={follower.name} secondary={follower.handle} />
                    </ListItem>
                ))}
            </List>
        </div>
    );
};

export default Followers;
