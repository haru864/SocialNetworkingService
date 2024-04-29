import React from 'react';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemText from '@mui/material/ListItemText';
import Typography from '@mui/material/Typography';

const trendsData = [
    { trend: "#React", tweets: "120K Tweets" },
    { trend: "#Nextjs", tweets: "80K Tweets" },
    { trend: "#JavaScript", tweets: "150K Tweets" }
];

const Tweets = () => {
    return (
        <div>
            <Typography variant="h5" gutterBottom>
                List of past tweets
            </Typography>
            <List>
                {trendsData.map((item, index) => (
                    <ListItem key={index}>
                        <ListItemText primary={item.trend} secondary={item.tweets} />
                    </ListItem>
                ))}
            </List>
        </div>
    );
};

export default Tweets;
