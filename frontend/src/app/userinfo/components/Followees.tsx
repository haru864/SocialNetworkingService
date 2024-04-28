import React from 'react';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemAvatar from '@mui/material/ListItemAvatar';
import Avatar from '@mui/material/Avatar';
import ListItemText from '@mui/material/ListItemText';
import Typography from '@mui/material/Typography';

const followeesData = [
    { name: "John Doe", handle: "@johndoe", avatar: "/path/to/avatar1.jpg" },
    { name: "Jane Smith", handle: "@janesmith", avatar: "/path/to/avatar2.jpg" },
    { name: "Alice Johnson", handle: "@alicejohn", avatar: "/path/to/avatar3.jpg" }
];

const Followees = () => {
    return (
        <div>
            <Typography variant="h5" gutterBottom>
                Your Followees
            </Typography>
            <List>
                {followeesData.map((followee, index) => (
                    <ListItem key={index}>
                        <ListItemAvatar>
                            <Avatar src={followee.avatar} />
                        </ListItemAvatar>
                        <ListItemText primary={followee.name} secondary={followee.handle} />
                    </ListItem>
                ))}
            </List>
        </div>
    );
};

export default Followees;
