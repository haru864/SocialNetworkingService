import React, { useState, useEffect } from 'react';
import Button from '@mui/material/Button';
import UserProfiles from './UserProfiles';
import Tweets from './Tweets';

const SearchResult = () => {
  const [tab, setTab] = useState<string>('users (name match)');
  return (
    <div style={{ width: '80%', padding: '20px' }}>
      <div style={{ marginBottom: '10px' }}>
        <Button
          variant="contained"
          color="primary"
          onClick={() => setTab('username')}
        >
          username
        </Button>
        <Button
          variant="contained"
          color="primary"
          onClick={() => setTab('address')}
        >
          address
        </Button>
        <Button
          variant="contained"
          color="primary"
          onClick={() => setTab('job')}
        >
          job
        </Button>
        <Button
          variant="contained"
          color="primary"
          onClick={() => setTab('hobby')}
        >
          hobby
        </Button>
        <Button
          variant="contained"
          color="primary"
          onClick={() => setTab('tweets')}
        >
          tweets
        </Button>
      </div>
      {tab === 'username' && <UserProfiles field='name' />}
      {tab === 'address' && <UserProfiles field='address' />}
      {tab === 'job' && <UserProfiles field='job' />}
      {tab === 'hobby' && <UserProfiles field='hobby' />}
      {tab === 'tweets' && <Tweets />}
    </div>
  );
};

export default SearchResult;
