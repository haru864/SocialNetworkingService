import React from 'react';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemText from '@mui/material/ListItemText';
import Divider from '@mui/material/Divider';

const Sidebar = () => {
    return (
        <div style={{ width: '20%', backgroundColor: '#1DA1F2', padding: '20px', color: 'white' }}>
            <h1>Social Networking Service</h1>
            <List component="nav" aria-label="main mailbox folders">
                <ListItem disablePadding>
                    <ListItemButton>
                        <ListItemText primary="Home" />
                    </ListItemButton>
                </ListItem>
                <ListItem disablePadding>
                    <ListItemButton>
                        <ListItemText primary="Profile" />
                    </ListItemButton>
                </ListItem>
                <ListItem disablePadding>
                    <ListItemButton>
                        <ListItemText primary="Notifications" />
                    </ListItemButton>
                </ListItem>
                <ListItem disablePadding>
                    <ListItemButton>
                        <ListItemText primary="Followers" />
                    </ListItemButton>
                </ListItem>
                <ListItem disablePadding>
                    <ListItemButton>
                        <ListItemText primary="Tweet" />
                    </ListItemButton>
                </ListItem>
                <ListItem disablePadding>
                    <ListItemButton>
                        <ListItemText primary="Messages" />
                    </ListItemButton>
                </ListItem>
            </List>
            <Divider />
        </div>
    );
};

export default Sidebar;
