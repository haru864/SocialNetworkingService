<?php

use Controllers\LoginController;
use Controllers\SignupController;
use Database\DataAccess\Implementations\AddressesDAOImpl;
use Database\DataAccess\Implementations\CareersDAOImpl;
use Database\DataAccess\Implementations\HobbiesDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Services\LoginService;
use Services\SignupService;

$usersDAOImpl = new UsersDAOImpl();
$addressesDAOImpl = new AddressesDAOImpl();
$careersDAOImpl = new CareersDAOImpl();
$hobbiesDAOImpl = new HobbiesDAOImpl();
$loginController = new LoginController(new LoginService($usersDAOImpl));
$signupController = new SignupController(new SignupService($usersDAOImpl, $addressesDAOImpl, $careersDAOImpl, $hobbiesDAOImpl));

$URL_DIR_PATTERN_LOGIN = '/^\/api\/login$/';
$URL_DIR_PATTERN_SIGNUP = '/^\/api\/signup$/';

return [
    $URL_DIR_PATTERN_LOGIN => $loginController,
    $URL_DIR_PATTERN_SIGNUP => $signupController,
];
