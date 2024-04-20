"use client"

import React, { useState } from 'react';
import Button from '@mui/material/Button';
import Trends from './Trends';
import Followers from './Followers';
import Timeline from './Timeline';

const ContentArea = () => {
  const [tab, setTab] = useState('timeline');
  return (
    <div style={{ width: '80%', padding: '20px' }}>
      <div>
        <Button variant="contained" color="primary" onClick={() => setTab('timeline')}>Timeline</Button>
        <Button variant="contained" color="primary" onClick={() => setTab('trends')}>Trends</Button>
        <Button variant="contained" color="primary" onClick={() => setTab('followers')}>Followers</Button>
      </div>
      {tab === 'timeline' && <Timeline />}
      {tab === 'trends' && <Trends />}
      {tab === 'followers' && <Followers />}
    </div>
  );
};

export default ContentArea;
