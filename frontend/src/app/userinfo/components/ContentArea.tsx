import React, { useState } from 'react';
import Button from '@mui/material/Button';
import Followers from './Followers';
import Tweets from './Tweets';
import Followees from './Followees';

const ContentArea = () => {
  const [tab, setTab] = useState('tweets');
  return (
    <div style={{ width: '80%', padding: '20px' }}>
      <div style={{ marginBottom: '10px' }}>
        <Button
          variant="contained"
          color="primary"
          onClick={() => setTab('tweets')}
        >
          Tweets
        </Button>
        <Button
          variant="contained"
          color="primary"
          onClick={() => setTab('followees')}
        >
          Followees
        </Button>
        <Button
          variant="contained"
          color="primary"
          onClick={() => setTab('followers')}
        >
          Followers
        </Button>
      </div>
      {tab === 'tweets' && <Tweets />}
      {tab === 'followees' && <Followees />}
      {tab === 'followers' && <Followers />}
    </div>
  );
};

export default ContentArea;
