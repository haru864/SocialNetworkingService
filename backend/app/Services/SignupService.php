<?php

namespace Services;

use Database\DataAccess\Implementations\UsersDAOImpl;
use Http\Request\SignupRequest;
use Models\User;
use Helpers\ValidationHelper;

class SignupService
{
    private UsersDAOImpl $usersDAOImpl;

    public function __construct(usersDAOImpl $usersDAOImpl)
    {
        $this->usersDAOImpl = $usersDAOImpl;
    }

    public function createUser(SignupRequest $request): User
    {
        $newUser = new User(
            id: null,
            name: $request->getUsername(),
            password_hash: password_hash($request->getPassword()),
            email: $request->getEmail(),
            self_introduction: ValidationHelper::isNonEmptyString($request->getSelfIntroduction()) ? $request->getSelfIntroduction() : null,
            profile_image: ValidationHelper::isNonEmptyString($request->getProfileImage()) ? $request->getProfileImage() : null,
            created_at: null,
            last_login: null,
            email_verified_at: null
        );
    }
}
