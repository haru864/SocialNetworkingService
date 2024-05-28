<?php

use Controllers\FollowController;
use Controllers\LikeController;
use Controllers\LiveMessageController;
use Controllers\LiveNotificationController;
use Controllers\LoginController;
use Controllers\LogoutController;
use Controllers\MessageController;
use Controllers\NotificationConfirmController;
use Controllers\NotificationController;
use Controllers\ProfileController;
use Controllers\ReplyController;
use Controllers\ResetPasswordController;
use Controllers\RetweetController;
use Controllers\SearchTweetsController;
use Controllers\SearchUsersController;
use Controllers\SignupController;
use Controllers\TweetController;
use Database\DataAccess\Implementations\AddressesDAOImpl;
use Database\DataAccess\Implementations\CareersDAOImpl;
use Database\DataAccess\Implementations\EmailVerificationDAOImpl;
use Database\DataAccess\Implementations\FollowNotificationDAOImpl;
use Database\DataAccess\Implementations\FollowsDAOImpl;
use Database\DataAccess\Implementations\HobbiesDAOImpl;
use Database\DataAccess\Implementations\LikeNotificationDAOImpl;
use Database\DataAccess\Implementations\LikesDAOImpl;
use Database\DataAccess\Implementations\MessageNotificationDAOImpl;
use Database\DataAccess\Implementations\MessagesDAOImpl;
use Database\DataAccess\Implementations\NotificationDAOImpl;
use Database\DataAccess\Implementations\PendingAddressesDAOImpl;
use Database\DataAccess\Implementations\PendingCareersDAOImpl;
use Database\DataAccess\Implementations\PendingHobbiesDAOImpl;
use Database\DataAccess\Implementations\PendingUsersDAOImpl;
use Database\DataAccess\Implementations\ReplyNotificationDAOImpl;
use Database\DataAccess\Implementations\RetweetNotificationDAOImpl;
use Database\DataAccess\Implementations\TweetsDAOImpl;
use Database\DataAccess\Implementations\ScheduledTweetsDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Middleware\AuthMiddleware;
use Middleware\NoopMiddleware;
use Services\FollowService;
use Services\LikeService;
use Services\LiveMessageService;
use Services\LiveNotificationService;
use Services\LoginService;
use Services\MessageService;
use Services\NotificationService;
use Services\ProfileService;
use Services\ReplyService;
use Services\ResetPasswordService;
use Services\RetweetService;
use Services\ScheduledTweetService;
use Services\SearchService;
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
$messagesDAOImpl = new MessagesDAOImpl();
$pendingUsersDAOImpl = new PendingUsersDAOImpl();
$pendingAddressesDAOImpl = new PendingAddressesDAOImpl();
$pendingCareersDAOImpl = new PendingCareersDAOImpl();
$pendingHobbiesDAOImpl = new PendingHobbiesDAOImpl();
$scheduledTweetsDAOImpl = new ScheduledTweetsDAOImpl();
$likeNotificationDAOImpl = new LikeNotificationDAOImpl();
$followNotificationDAOImpl = new FollowNotificationDAOImpl();
$messageNotificationDAOImpl = new MessageNotificationDAOImpl();
$replyNotificationDAOImpl = new ReplyNotificationDAOImpl();
$retweetNotificationDAOImpl = new RetweetNotificationDAOImpl();
$notificationDAOImpl = new NotificationDAOImpl();

$loginService = new LoginService($usersDAOImpl);
$signupService = new SignupService(
    $usersDAOImpl,
    $addressesDAOImpl,
    $careersDAOImpl,
    $hobbiesDAOImpl,
    $pendingUsersDAOImpl,
    $pendingAddressesDAOImpl,
    $pendingCareersDAOImpl,
    $pendingHobbiesDAOImpl,
    $emailVerificationDAOImpl
);
$tweetService = new TweetService($tweetsDAOImpl);
$scheduledTweetService = new ScheduledTweetService($scheduledTweetsDAOImpl, $tweetsDAOImpl);
$retweetService = new RetweetService($tweetsDAOImpl);
$replyService = new ReplyService($tweetsDAOImpl);
$likeService = new LikeService($likesDAOImpl);
$followService = new FollowService($usersDAOImpl, $followsDAOImpl);
$profileService = new ProfileService(
    $usersDAOImpl,
    $addressesDAOImpl,
    $careersDAOImpl,
    $hobbiesDAOImpl,
    $pendingUsersDAOImpl,
    $pendingAddressesDAOImpl,
    $pendingCareersDAOImpl,
    $pendingHobbiesDAOImpl,
    $emailVerificationDAOImpl,
    $followsDAOImpl
);
$messageService = new MessageService($messagesDAOImpl, $usersDAOImpl);
$resetPasswordService = new ResetPasswordService($usersDAOImpl, $emailVerificationDAOImpl);
$liveMessageService = new LiveMessageService();
$notificationService = new NotificationService(
    $notificationDAOImpl,
    $likeNotificationDAOImpl,
    $followNotificationDAOImpl,
    $messageNotificationDAOImpl,
    $replyNotificationDAOImpl,
    $retweetNotificationDAOImpl
);
$liveNotificationService = new LiveNotificationService(
    $likeNotificationDAOImpl,
    $followNotificationDAOImpl,
    $messageNotificationDAOImpl,
    $replyNotificationDAOImpl,
    $retweetNotificationDAOImpl,
    $tweetsDAOImpl
);
$searchService = new SearchService($usersDAOImpl, $tweetsDAOImpl);

$loginController = new LoginController($loginService);
$signupController = new SignupController($signupService);
$logoutController = new LogoutController();
$tweetController = new TweetController($tweetService, $scheduledTweetService);
$retweetController = new RetweetController($retweetService, $liveNotificationService);
$replyController  = new ReplyController($replyService, $liveNotificationService);
$likeController = new LikeController($likeService, $liveNotificationService);
$followController = new FollowController($followService, $liveNotificationService);
$profileController = new ProfileController($profileService, $signupService);
$messageController = new MessageController($messageService, $profileService, $liveMessageService, $liveNotificationService);
$resetPasswordController = new ResetPasswordController($resetPasswordService);
$liveMessageController = new LiveMessageController($liveMessageService);
$notificationController = new NotificationController($notificationService);
$liveNotificationController = new LiveNotificationController($liveNotificationService);
$notificationConfirmController = new NotificationConfirmController($notificationService);
$searchUsersController = new SearchUsersController($searchService);
$searchTweetsController = new SearchTweetsController($searchService);

$URL_DIR_PATTERN_LOGIN = '/^\/api\/login$/';
$URL_DIR_PATTERN_RESET_PASSWORD = '/^\/api\/reset_password$/';
$URL_DIR_PATTERN_SIGNUP = '/^\/api\/signup$/';
$URL_DIR_PATTERN_VALIDATE_SIGNUP_EMAIL = '/^\/api\/signup\/validate_email$/';
$URL_DIR_PATTERN_LOGOUT = '/^\/api\/logout$/';
$URL_DIR_PATTERN_TWEETS = '/^\/api\/tweets(\/([1-9][0-9]*))?$/';
$URL_DIR_PATTERN_RETWEETS = '/^\/api\/tweets\/(\d+)\/retweets$/';
$URL_DIR_PATTERN_REPLIES = '/^\/api\/tweets\/(\d+)\/replies$/';
$URL_DIR_PATTERN_LIKES = '/^\/api\/likes$/';
$URL_DIR_PATTERN_FOLLOWS = '/^\/api\/follows\/(follower|followee|follow)$/';
$URL_DIR_PATTERN_PROFILE = '/^\/api\/profile$/';
$URL_DIR_PATTERN_VALIDATE_UPDATE_EMAIL = '/^\/api\/profile\/validate_email$/';
$URL_DIR_PATTERN_MESSAGES = '/^\/api\/messages(\/([1-9][0-9]*))??$/';
$URL_DIR_PATTERN_MESSAGES_LIVE = '/^\/api\/live\/messages\/([1-9][0-9]*)$/';
$URL_DIR_PATTERN_NOTIFICATIONS = '/^\/api\/notifications$/';
$URL_DIR_PATTERN_NOTIFICATIONS_LIVE = '/^\/api\/live\/notifications$/';
$URL_DIR_PATTERN_NOTIFICATIONS_CONFIRM = '/^\/api\/notifications\/confirm$/';
$URL_DIR_PATTERN_SEARCH_USERS = '/^\/api\/search\/users$/';
$URL_DIR_PATTERN_SEARCH_TWEETS = '/^\/api\/search\/tweets$/';

// TODO Nginxのauth_requestモジュールでリクエストを認証用PHPスクリプトに送り、メディアファイルアクセスを許可または拒否する
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
    $URL_DIR_PATTERN_VALIDATE_SIGNUP_EMAIL => [
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
    $URL_DIR_PATTERN_VALIDATE_UPDATE_EMAIL => [
        'controller' => $profileController,
        'middleware' => new AuthMiddleware()
    ],
    $URL_DIR_PATTERN_MESSAGES => [
        'controller' => $messageController,
        'middleware' => new AuthMiddleware()
    ],
    $URL_DIR_PATTERN_MESSAGES_LIVE => [
        'controller' => $liveMessageController,
        'middleware' => new AuthMiddleware()
    ],
    $URL_DIR_PATTERN_NOTIFICATIONS => [
        'controller' => $notificationController,
        'middleware' => new AuthMiddleware()
    ],
    $URL_DIR_PATTERN_NOTIFICATIONS_LIVE => [
        'controller' => $liveNotificationController,
        'middleware' => new AuthMiddleware()
    ],
    $URL_DIR_PATTERN_NOTIFICATIONS_CONFIRM => [
        'controller' => $notificationConfirmController,
        'middleware' => new AuthMiddleware()
    ],
    $URL_DIR_PATTERN_SEARCH_USERS => [
        'controller' => $searchUsersController,
        'middleware' => new AuthMiddleware()
    ],
    $URL_DIR_PATTERN_SEARCH_TWEETS => [
        'controller' => $searchTweetsController,
        'middleware' => new AuthMiddleware()
    ],
];
