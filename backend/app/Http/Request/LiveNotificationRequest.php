<?php

namespace Http\Request;

use Exceptions\InvalidRequestURIException;

class LiveNotificationRequest
{
    public function __construct()
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $ltrimedUriDir = ltrim($uriDir, "/");
        $uriDirArr = explode('/', $ltrimedUriDir);
        $urlDirCount = 3;
        $liveIndex = 1;
        if (count($uriDirArr) !== $urlDirCount || $uriDirArr[$liveIndex] !== 'live') {
            throw new InvalidRequestURIException('Invalid request URI.');
        }
    }
}
