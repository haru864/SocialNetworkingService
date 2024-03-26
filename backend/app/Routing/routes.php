<?php

use Controllers\LoginController;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Services\LoginService;

$usersDAOImpl = new UsersDAOImpl();
$loginController = new LoginController(new LoginService($usersDAOImpl));

$URL_DIR_PATTERN_LOGIN = '/^\/api\/login$/';

return [
    $URL_DIR_PATTERN_LOGIN => $loginController,
];
