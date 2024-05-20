<?php

namespace Http\Request;

class NotificationRequest
{
    private bool $isLiveNotification;

    public function __construct()
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $ltrimedUriDir = ltrim($uriDir, "/");
        $uriDirArr = explode('/', $ltrimedUriDir);
        $urlDirCount = 3;
        $liveIndex = 1;
        if (count($uriDirArr) === $urlDirCount && $uriDirArr[$liveIndex] === 'live') {
            $this->isLiveNotification = true;
        } else {
            $this->isLiveNotification = false;
        }
    }

    public function getIsLiveNotification(): bool
    {
        return $this->isLiveNotification;
    }
}
