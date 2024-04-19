<?php

use Controllers\FollowController;
use Controllers\LikeController;
use Controllers\LoginController;
use Controllers\LogoutController;
use Controllers\MessageController;
use Controllers\ProfileController;
use Controllers\ReplyController;
use Controllers\ResetPasswordController;
use Controllers\RetweetController;
use Controllers\SignupController;
use Controllers\TweetController;
use Database\DataAccess\Implementations\AddressesDAOImpl;
use Database\DataAccess\Implementations\CareersDAOImpl;
use Database\DataAccess\Implementations\EmailVerificationDAOImpl;
use Database\DataAccess\Implementations\FollowsDAOImpl;
use Database\DataAccess\Implementations\HobbiesDAOImpl;
use Database\DataAccess\Implementations\LikesDAOImpl;
use Database\DataAccess\Implementations\MessagesDAOImpl;
use Database\DataAccess\Implementations\TweetsDAOImpl;
use Database\DataAccess\Implementations\RetweetsDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Middleware\AuthMiddleware;
use Middleware\NoopMiddleware;
use Services\FollowService;
use Services\LikeService;
use Services\LoginService;
use Services\MessageService;
use Services\ProfileService;
use Services\ReplyService;
use Services\ResetPasswordService;
use Services\RetweetService;
use Services\SignupService;
use Services\TweetService;

$usersDAOImpl = new UsersDAOImpl();
$addressesDAOImpl = new AddressesDAOImpl();
$careersDAOImpl = new CareersDAOImpl();
$hobbiesDAOImpl = new HobbiesDAOImpl();
$emailVerificationDAOImpl = new EmailVerificationDAOImpl();
$tweetsDAOImpl = new TweetsDAOImpl();
$retweetsDAOImpl = new RetweetsDAOImpl();
$likesDAOImpl = new LikesDAOImpl();
$followsDAOImpl = new FollowsDAOImpl();
$messagesDAOImpl = new MessagesDAOImpl();

$loginService = new LoginService($usersDAOImpl);
$signupService = new SignupService($usersDAOImpl, $addressesDAOImpl, $careersDAOImpl, $hobbiesDAOImpl, $emailVerificationDAOImpl);
$tweetService = new TweetService($tweetsDAOImpl);
$retweetService = new RetweetService($retweetsDAOImpl);
$replyService = new ReplyService($tweetsDAOImpl);
$likeService = new LikeService($likesDAOImpl);
$followService = new FollowService($followsDAOImpl);
$profileService = new ProfileService($usersDAOImpl, $addressesDAOImpl, $careersDAOImpl, $hobbiesDAOImpl);
$messageService = new MessageService($messagesDAOImpl, $usersDAOImpl);
$resetPasswordService = new ResetPasswordService($usersDAOImpl, $emailVerificationDAOImpl);

$loginController = new LoginController($loginService);
$signupController = new SignupController($signupService);
$logoutController = new LogoutController();
$tweetController = new TweetController($tweetService);
$retweetController = new RetweetController($retweetService);
$replyController  = new ReplyController($replyService);
$likeController = new LikeController($likeService);
$followController = new FollowController($followService);
$profileController = new ProfileController($profileService, $signupService);
$messageController = new MessageController($messageService);
$resetPasswordController = new ResetPasswordController($resetPasswordService);

$URL_DIR_PATTERN_LOGIN = '/^\/api\/login$/';
$URL_DIR_PATTERN_RESET_PASSWORD = '/^\/api\/reset_password$/';
$URL_DIR_PATTERN_SIGNUP = '/^\/api\/signup$/';
$URL_DIR_PATTERN_VALIDATE_EMAIL = '/^\/api\/validate_email$/';
$URL_DIR_PATTERN_LOGOUT = '/^\/api\/logout$/';
$URL_DIR_PATTERN_TWEETS = '/^\/api\/tweets$/';
$URL_DIR_PATTERN_RETWEETS = '/^\/api\/tweets\/(\d+)\/retweets$/';
$URL_DIR_PATTERN_REPLIES = '/^\/api\/tweets\/(\d+)\/replies$/';
$URL_DIR_PATTERN_LIKES = '/^\/api\/likes$/';
$URL_DIR_PATTERN_FOLLOWS = '/^\/api\/follows$/';
$URL_DIR_PATTERN_PROFILE = '/^\/api\/profile$/';
$URL_DIR_PATTERN_MESSAGES = '/^\/api\/messages(\/\d+)?$/';

return [
    $URL_DIR_PATTERN_LOGIN => [
        'controller' => $loginController,
        'middleware' => new NoopMiddleware()
    ],
    $URL_DIR_PATTERN_RESET_PASSWORD => [
        'controller' => $resetPasswordController,
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
    $URL_DIR_PATTERN_RETWEETS => [
        'controller' => $retweetController,
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
    $URL_DIR_PATTERN_PROFILE => [
        'controller' => $profileController,
        'middleware' => new AuthMiddleware()
    ],
    $URL_DIR_PATTERN_MESSAGES => [
        'controller' => $messageController,
        'middleware' => new AuthMiddleware()
    ],
    // TODO 通知機能を実装する
    // TODO 予約投稿機能を実装する
];
