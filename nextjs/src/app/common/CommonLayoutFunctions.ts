import { styled, alpha } from '@mui/material/styles';
import InputBase from '@mui/material/InputBase';
import { getNotifications } from '../notifications/components/NotificationFunctions';

export const Search = styled('div')(({ theme }) => ({
    position: 'relative',
    borderRadius: theme.shape.borderRadius,
    backgroundColor: alpha(theme.palette.common.white, 0.15),
    '&:hover': {
        backgroundColor: alpha(theme.palette.common.white, 0.25),
    },
    marginLeft: 0,
    width: '100%',
    [theme.breakpoints.up('sm')]: {
        marginLeft: theme.spacing(1),
        width: 'auto',
    },
}));

export const SearchIconWrapper = styled('div')(({ theme }) => ({
    padding: theme.spacing(0, 2),
    height: '100%',
    position: 'absolute',
    pointerEvents: 'none',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
}));

export const StyledInputBase = styled(InputBase)(({ theme }) => ({
    color: 'inherit',
    width: '100%',
    '& .MuiInputBase-input': {
        padding: theme.spacing(1, 1, 1, 0),
        paddingLeft: `calc(1em + ${theme.spacing(4)})`,
        transition: theme.transitions.create('width'),
        [theme.breakpoints.up('sm')]: {
            width: '12ch',
            '&:focus': {
                width: '20ch',
            },
        },
    },
}));

export async function hasUnconfirmedNotifications(): Promise<boolean> {
    const newestNotificationsPage = 1;
    const recentNotificationDTOs = await getNotifications(newestNotificationsPage);
    if (recentNotificationDTOs.length === 0) {
        return false;
    }
    const newestNotificationDTO = recentNotificationDTOs[0];
    return !newestNotificationDTO.isConfirmed;
}

export async function getLoginUserId(): Promise<number> {
    try {
        const response = await fetch(`${process.env.API_DOMAIN}/api/check_session`, {
            method: 'GET',
            credentials: 'include',
        });
        if (response.status !== 200) {
            window.location.href = `/error/invalid_session`;
            throw new Error('Invalid session');
        }
        const respData = await response.json();
        return respData['user_id'] as number;
    } catch (error: any) {
        console.error(error);
        alert('An error occurred while fetching the login user ID');
        throw error;
    }
};
