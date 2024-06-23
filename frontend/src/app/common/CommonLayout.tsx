'use client'

import React, { useEffect, useState, useRef } from 'react';
import AccountBoxIcon from '@mui/icons-material/AccountBox';
import AppBar from '@mui/material/AppBar';
import Box from '@mui/material/Box';
import CssBaseline from '@mui/material/CssBaseline';
import DashboardIcon from '@mui/icons-material/Dashboard';
import Divider from '@mui/material/Divider';
import Drawer from '@mui/material/Drawer';
import HomeIcon from '@mui/icons-material/Home';
import IconButton from '@mui/material/IconButton';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListItemText from '@mui/material/ListItemText';
import Link from '@mui/material/Link';
import MenuIcon from '@mui/icons-material/Menu';
import NotificationsIcon from '@mui/icons-material/Notifications';
import MessageIcon from '@mui/icons-material/Message';
import QuestionAnswerIcon from '@mui/icons-material/QuestionAnswer';
import LogoutIcon from '@mui/icons-material/Logout';
import Toolbar from '@mui/material/Toolbar';
import Typography from '@mui/material/Typography';
import SearchIcon from '@mui/icons-material/Search';
import Copyright from './copyright';
import Badge from '@mui/material/Badge';
import {
    Search, SearchIconWrapper, StyledInputBase,
    hasUnconfirmedNotifications, getLoginUserId
} from './CommonLayoutFunctions';
import { NotificationDTO } from '../notifications/components/NotificationDTO';

const drawerWidth = 230;

interface Props {
    /**
     * Injected by the documentation to work in an iframe.
     * Remove this when copying and pasting into your project.
     */
    window?: () => Window;
}

interface CommonLayoutProps {
    children: React.ReactNode;
}

const CommonLayout: React.FC<CommonLayoutProps> = ({ children }) => {
    const [mobileOpen, setMobileOpen] = React.useState(false);
    const [isClosing, setIsClosing] = React.useState(false);
    const [newNotification, setNewNotification] = React.useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    const [loginUserId, setLoginUserId] = useState<number | null>(null);

    useEffect(() => {
        const fetchData = async () => {
            const result = await hasUnconfirmedNotifications();
            setNewNotification(result);
            const userId = await getLoginUserId();
            setLoginUserId(userId);
        };
        fetchData();
    }, []);

    useEffect(() => {
        if (loginUserId === null) {
            return;
        }
        const cleanup = checkNotificationInRealTime(loginUserId);
        return cleanup;
    }, [loginUserId]);

    const checkNotificationInRealTime = (userId: number) => {
        const eventSource = new EventSource(`${process.env.SSE_NOTIFICATION_URL}?user_id=${userId}`);
        eventSource.onmessage = (event: MessageEvent) => {
            const data = JSON.parse(event.data);
            new NotificationDTO(data);
            setNewNotification(true);
        };
        eventSource.onerror = (error) => {
            console.error('EventSource failed:', error);
            eventSource.close();
        };
        return () => {
            eventSource.close();
        };
    };

    const handleDrawerClose = () => {
        setIsClosing(true);
        setMobileOpen(false);
    };

    const handleDrawerTransitionEnd = () => {
        setIsClosing(false);
    };

    const handleDrawerToggle = () => {
        if (!isClosing) {
            setMobileOpen(!mobileOpen);
        }
    };

    const handleSearchInputChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        setSearchQuery(event.target.value);
    };

    const handleSearchSubmit = (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        if (searchQuery.trim()) {
            window.location.href = `/search?query=${encodeURIComponent(searchQuery.trim())}`;
        }
    };

    const menuItems = [
        { name: 'Home', path: '/home', icon: <HomeIcon /> },
        { name: 'Profile', path: '/profile', icon: <AccountBoxIcon /> },
        { name: 'Notifications', path: '/notifications', icon: <NotificationsIcon /> },
        { name: 'Dashboard', path: '/dashboard', icon: <DashboardIcon /> },
        { name: 'Tweet', path: '/tweet/post', icon: <MessageIcon /> },
        { name: 'Messages', path: '/messages/chatlist', icon: <QuestionAnswerIcon /> },
        { name: 'Log out', path: '/logout', icon: <LogoutIcon /> },
    ];
    const drawer = (
        <div>
            <Toolbar />
            <Divider />
            <List>
                {menuItems.map((item) => (
                    <Link key={item.name} href={item.path} variant="body2">
                        <ListItem key={item.name} disablePadding>
                            <ListItemButton>
                                <ListItemIcon>
                                    {item.path === '/notifications' ? (
                                        <Badge
                                            color="secondary"
                                            variant="dot"
                                            invisible={!newNotification}
                                        >
                                            {item.icon}
                                        </Badge>
                                    ) : (
                                        item.icon
                                    )}
                                </ListItemIcon>
                                <ListItemText primary={item.name} />
                            </ListItemButton>
                        </ListItem>
                    </Link>
                ))}
            </List>
        </div>
    );

    return (
        <Box sx={{ display: 'flex' }}>
            <CssBaseline />
            <AppBar
                position="fixed"
                sx={{
                    width: { sm: `calc(100% - ${drawerWidth}px)` },
                    ml: { sm: `${drawerWidth}px` },
                }}
            >
                <Toolbar>
                    <IconButton
                        color="inherit"
                        aria-label="open drawer"
                        edge="start"
                        onClick={handleDrawerToggle}
                        sx={{ mr: 2, display: { sm: 'none' } }}
                    >
                        <MenuIcon />
                    </IconButton>
                    <Typography
                        variant="h6"
                        noWrap
                        component="div"
                        sx={{ flexGrow: 1, display: { xs: 'none', sm: 'block', width: '100%' } }}
                    >
                        Social Networking Service
                    </Typography>
                    <Box sx={{ display: 'flex', justifyContent: 'flex-end', width: '100%' }}>
                        <form onSubmit={handleSearchSubmit}>
                            <Search>
                                <SearchIconWrapper>
                                    <SearchIcon />
                                </SearchIconWrapper>
                                <StyledInputBase
                                    placeholder="Search…"
                                    inputProps={{ 'aria-label': 'search' }}
                                    value={searchQuery}
                                    onChange={handleSearchInputChange}
                                />
                            </Search>
                        </form>
                    </Box>
                </Toolbar>
            </AppBar>
            <Box
                component="nav"
                sx={{ width: { sm: drawerWidth }, flexShrink: { sm: 0 } }}
                aria-label="mailbox folders"
            >
                {/* The implementation can be swapped with js to avoid SEO duplication of links. */}
                <Drawer
                    variant="temporary"
                    open={mobileOpen}
                    onTransitionEnd={handleDrawerTransitionEnd}
                    onClose={handleDrawerClose}
                    ModalProps={{
                        keepMounted: true, // Better open performance on mobile.
                    }}
                    sx={{
                        display: { xs: 'block', sm: 'none' },
                        '& .MuiDrawer-paper': { boxSizing: 'border-box', width: drawerWidth },
                    }}
                >
                    {drawer}
                </Drawer>
                <Drawer
                    variant="permanent"
                    sx={{
                        display: { xs: 'none', sm: 'block' },
                        '& .MuiDrawer-paper': { boxSizing: 'border-box', width: drawerWidth },
                    }}
                    open
                >
                    {drawer}
                </Drawer>
            </Box>
            <Box
                component="main"
                sx={{ flexGrow: 1, p: 3, width: { sm: `calc(100% - ${drawerWidth}px)` } }}
            >
                <Toolbar />
                {children}
                <Copyright />
            </Box>
        </Box >
    );
};

export default CommonLayout;
