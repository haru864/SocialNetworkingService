<?php

use Controllers\FollowController;
use Controllers\LikeController;
use Controllers\LoginController;
use Controllers\LogoutController;
use Controllers\ReplyController;
use Controllers\SignupController;
use Controllers\TweetController;
use Database\DataAccess\Implementations\AddressesDAOImpl;
use Database\DataAccess\Implementations\CareersDAOImpl;
use Database\DataAccess\Implementations\EmailVerificationDAOImpl;
use Database\DataAccess\Implementations\FollowsDAOImpl;
use Database\DataAccess\Implementations\HobbiesDAOImpl;
use Database\DataAccess\Implementations\LikesDAOImpl;
use Database\DataAccess\Implementations\TweetsDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Middleware\AuthMiddleware;
use Middleware\NoopMiddleware;
use Services\FollowService;
use Services\LikeService;
use Services\LoginService;
use Services\ReplyService;
use Services\SignupService;
use Services\TweetService;

$usersDAOImpl = new UsersDAOImpl();
$addressesDAOImpl = new AddressesDAOImpl();
$careersDAOImpl = new CareersDAOImpl();
$hobbiesDAOImpl = new HobbiesDAOImpl();
$emailVerificationDAOImpl = new EmailVerificationDAOImpl();
$tweetsDAOImpl = new TweetsDAOImpl();
$likesDAOImpl = new LikesDAOImpl();
$followsDAOImpl = new FollowsDAOImpl();
$loginController = new LoginController(new LoginService($usersDAOImpl));
$signupController = new SignupController(new SignupService($usersDAOImpl, $addressesDAOImpl, $careersDAOImpl, $hobbiesDAOImpl, $emailVerificationDAOImpl));
$logoutController = new LogoutController();
$tweetController = new TweetController(new TweetService($tweetsDAOImpl));
$replyController  = new ReplyController(new ReplyService($tweetsDAOImpl));
$likeController = new LikeController(new LikeService($likesDAOImpl));
$followController = new FollowController(new FollowService($followsDAOImpl));

$URL_DIR_PATTERN_LOGIN = '/^\/api\/login$/';
$URL_DIR_PATTERN_SIGNUP = '/^\/api\/signup$/';
$URL_DIR_PATTERN_VALIDATE_EMAIL = '/^\/api\/validate$/';
$URL_DIR_PATTERN_LOGOUT = '/^\/api\/logout$/';
$URL_DIR_PATTERN_TWEETS = '/^\/api\/tweets$/';
$URL_DIR_PATTERN_RETWEETS = '/^\/api\/tweets\/(\d+)\/retweets$/';
$URL_DIR_PATTERN_REPLIES = '/^\/api\/tweets\/(\d+)\/replies$/';
$URL_DIR_PATTERN_LIKES = '/^\/api\/likes$/';
$URL_DIR_PATTERN_FOLLOWS = '/^\/api\/follows$/';

return [
    $URL_DIR_PATTERN_LOGIN => [
        'controller' => $loginController,
        'middleware' => new NoopMiddleware()
    ],
    $URL_DIR_PATTERN_SIGNUP => [
        'controller' => $signupController,
        'middleware' => new NoopMiddleware()
    ],
    $URL_DIR_PATTERN_VALIDATE_EMAIL => [
        'controller' => $signupController,
        'middleware' => new NoopMiddleware()
    ],
    $URL_DIR_PATTERN_LOGOUT => [
        'controller' => $logoutController,
        'middleware' => new AuthMiddleware()
    ],
    $URL_DIR_PATTERN_TWEETS => [
        'controller' => $tweetController,
        'middleware' => new AuthMiddleware()
    ],
    $URL_DIR_PATTERN_REPLIES => [
        'controller' => $replyController,
        'middleware' => new AuthMiddleware()
    ],
    $URL_DIR_PATTERN_LIKES => [
        'controller' => $likeController,
        'middleware' => new AuthMiddleware()
    ],
    $URL_DIR_PATTERN_FOLLOWS => [
        'controller' => $followController,
        'middleware' => new AuthMiddleware()
    ],
];
