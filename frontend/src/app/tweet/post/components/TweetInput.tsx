import { useState } from 'react';
import {
    Button,
    TextField,
    Box,
    Typography,
    Grid,
    CircularProgress,
    FormControlLabel,
    Checkbox
} from '@mui/material';
import * as ValidationUtil from "../../../utils/ValidationUtil";

async function postTweet(
    event: React.FormEvent<HTMLFormElement>,
    setLoading: React.Dispatch<React.SetStateAction<boolean>>
) {
    try {
        event.preventDefault();
        const data: FormData = new FormData(event.currentTarget);
        let dateTime: string | null = data.get('dateTime') as string | null;
        if (dateTime) {
            dateTime = dateTime.replace('T', ' ') + ':00';
            data.set('dateTime', dateTime);
        }
        const message: string = data.get('message') as string;
        ValidationUtil.validateCharCount(message, "Message", 1, 200);
        setLoading(true);
        const response = await fetch(`${process.env.API_DOMAIN}/api/tweets`, {
            method: 'POST',
            body: data
        });
        setLoading(false);
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData["error_message"]);
        }
        alert('Tweet posted.');
    } catch (error: any) {
        console.error(error);
        alert(error);
    }
}

export default function TweetInput() {
    const [isChecked, setIsChecked] = useState(false);
    const [dateTime, setDateTime] = useState('');
    const [loading, setLoading] = useState(false);
    const handleCheckboxChange = (event: any) => {
        setIsChecked(event.target.checked);
    };
    if (loading) {
        return (
            <Box display="flex" justifyContent="center" alignItems="center" height="100vh">
                <CircularProgress />
            </Box>
        );
    }
    return (
        <Box component="form" onSubmit={(e) => postTweet(e, setLoading)} sx={{ mt: 1 }}>
            <Grid container spacing={2}>
                <Typography variant="h6">Create a new post</Typography>
                <Grid item xs={12}>
                    <TextField
                        fullWidth
                        name="message"
                        label="Message"
                        id="message"
                        autoComplete=""
                        multiline
                        minRows={3}
                        maxRows={5}
                    />
                </Grid>
                <Grid item xs={12} style={{ display: 'flex', justifyContent: 'center' }}>
                    <input
                        accept="image/jpeg, image/png, image/gif, video/mp4, video/webm, video/ogg"
                        type="file"
                        id="upload_file_button"
                        name="media"
                        style={{ display: 'none' }}
                    />
                    <label htmlFor="upload_file_button">
                        <Button variant="contained" component="span">
                            Upload Image/Video
                        </Button>
                    </label>
                </Grid>
                <Grid item xs={12} style={{ display: 'flex', justifyContent: 'center' }} sx={{ mt: 1 }}>
                    <FormControlLabel
                        control={<Checkbox checked={isChecked} onChange={handleCheckboxChange} />}
                        label="Specify post date and time"
                    />
                    {isChecked && (
                        <TextField
                            name="dateTime"
                            label="Post Date and Time"
                            type="datetime-local"
                            value={dateTime}
                            onChange={e => setDateTime(e.target.value)}
                            InputLabelProps={{
                                shrink: true,
                            }}
                        />
                    )}
                </Grid>
                <Grid item xs={12} style={{ display: 'flex', justifyContent: 'center' }}>
                    <Button type="submit" variant="contained" sx={{ mb: 2 }}>
                        Tweet
                    </Button>
                </Grid>
            </Grid>
        </Box>
    );
}
