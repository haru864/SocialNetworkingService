"use client"

import React, { useState } from 'react';
import Button from '@mui/material/Button';
import Trends from './Trends';
import Followers from './Followers';

const ContentArea = () => {
  const [tab, setTab] = useState('trends');
  return (
    <div style={{ width: '80%', padding: '20px' }}>
      <div style={{ marginBottom: '10px' }}>
        <Button
          variant="contained"
          color="primary"
          onClick={() => setTab('trends')}
        >
          Trends
        </Button>
        <Button
          variant="contained"
          color="primary"
          onClick={() => setTab('followers')}
        >
          Followers
        </Button>
      </div>
      {tab === 'trends' && <Trends />}
      {tab === 'followers' && <Followers />}
    </div>
  );
};

export default ContentArea;
