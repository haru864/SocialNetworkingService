<?php

namespace Services;

use Database\DataAccess\Implementations\AddressesDAOImpl;
use Database\DataAccess\Implementations\CareersDAOImpl;
use Database\DataAccess\Implementations\EmailVerificationDAOImpl;
use Database\DataAccess\Implementations\FollowsDAOImpl;
use Database\DataAccess\Implementations\HobbiesDAOImpl;
use Database\DataAccess\Implementations\PendingAddressesDAOImpl;
use Database\DataAccess\Implementations\PendingCareersDAOImpl;
use Database\DataAccess\Implementations\PendingHobbiesDAOImpl;
use Database\DataAccess\Implementations\PendingUsersDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InternalServerException;
use Exceptions\InvalidRequestParameterException;
use Helpers\FileUtility;
use Helpers\MailUtility;
use Helpers\SessionManager;
use Helpers\ValidationHelper;
use Http\Request\PostProfileRequest;
use Models\Career;
use Models\Hobby;
use Models\PendingAddress;
use Models\PendingCareer;
use Models\PendingHobby;
use Models\PendingUser;
use Models\User;
use Settings\Settings;
use Throwable;

class ProfileService
{
    private UsersDAOImpl $usersDAOImpl;
    private AddressesDAOImpl $addressesDAOImpl;
    private CareersDAOImpl $careersDAOImpl;
    private HobbiesDAOImpl $hobbiesDAOImpl;
    private PendingUsersDAOImpl $pendingUsersDAOImpl;
    private PendingAddressesDAOImpl $pendingAddressesDAOImpl;
    private PendingCareersDAOImpl $pendingCareersDAOImpl;
    private PendingHobbiesDAOImpl $pendingHobbiesDAOImpl;
    private EmailVerificationDAOImpl $emailVerificationDAOImpl;
    private FollowsDAOImpl $followsDAOImpl;

    public function __construct(
        usersDAOImpl $usersDAOImpl,
        AddressesDAOImpl $addressesDAOImpl,
        CareersDAOImpl $careersDAOImpl,
        HobbiesDAOImpl $hobbiesDAOImpl,
        PendingUsersDAOImpl $pendingUsersDAOImpl,
        PendingAddressesDAOImpl $pendingAddressesDAOImpl,
        PendingCareersDAOImpl $pendingCareersDAOImpl,
        PendingHobbiesDAOImpl $pendingHobbiesDAOImpl,
        EmailVerificationDAOImpl $emailVerificationDAOImpl,
        FollowsDAOImpl $followsDAOImpl
    ) {
        $this->usersDAOImpl = $usersDAOImpl;
        $this->addressesDAOImpl = $addressesDAOImpl;
        $this->careersDAOImpl = $careersDAOImpl;
        $this->hobbiesDAOImpl = $hobbiesDAOImpl;
        $this->pendingUsersDAOImpl = $pendingUsersDAOImpl;
        $this->pendingAddressesDAOImpl = $pendingAddressesDAOImpl;
        $this->pendingCareersDAOImpl = $pendingCareersDAOImpl;
        $this->pendingHobbiesDAOImpl = $pendingHobbiesDAOImpl;
        $this->emailVerificationDAOImpl = $emailVerificationDAOImpl;
        $this->followsDAOImpl = $followsDAOImpl;
    }

    public function createPendingUser(PostProfileRequest $request, int $userId): PendingUser
    {
        $currentUser = $this->usersDAOImpl->getById($userId);
        if (is_null($currentUser)) {
            throw new InvalidRequestParameterException('User not found.');
        }
        $isUsernameChanged = $currentUser->getName() !== $request->getUsername();
        $isUsernameUsed = ($this->usersDAOImpl->getByName($request->getUsername()) !== null
            || $this->pendingUsersDAOImpl->getByName($request->getUsername()) !== null);
        if ($isUsernameChanged && $isUsernameUsed) {
            throw new InvalidRequestParameterException("Specified username is already used.");
        }
        try {
            $profileImage = null;
            if ($request->getProfileImage() !== null) {
                $profileImage = FileUtility::storeImageWithThumbnail(
                    storeDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_UPLOAD'),
                    thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_THUMBNAIL'),
                    uploadedTmpFilePath: $request->getProfileImage()['tmp_name'],
                    uploadedFileName: $request->getProfileImage()['name'],
                    thumbWidth: 100
                );
            }
            $pendingUser = new PendingUser(
                id: null,
                userId: $userId,
                name: $request->getUsername(),
                password_hash: password_hash($request->getPassword(), PASSWORD_DEFAULT),
                email: $request->getEmail(),
                self_introduction: ValidationHelper::isNonEmptyString($request->getSelfIntroduction()) ? $request->getSelfIntroduction() : null,
                profile_image: $profileImage,
            );
            $pendingUserInTable = null;
            $pendingUserInTable = $this->pendingUsersDAOImpl->create($pendingUser);

            if (
                $request->getCountry() !== ''
                || $request->getState() !== ''
                || $request->getCity() !== ''
                || $request->getTown() !== ''
            ) {
                $pendingAddress = new PendingAddress(
                    id: null,
                    pendingUserId: $pendingUserInTable->getId(),
                    country: $request->getCountry(),
                    state: $request->getState(),
                    city: $request->getCity(),
                    town: $request->getTown()
                );
                $this->pendingAddressesDAOImpl->create($pendingAddress);
            }

            foreach ($request->getCareers() as $job) {
                $pendingCareer = new PendingCareer(
                    id: null,
                    pendingUserId: $pendingUserInTable->getId(),
                    job: $job
                );
                $this->pendingCareersDAOImpl->create($pendingCareer);
            }

            foreach ($request->getHobbies() as $hobby) {
                $pendingHobby = new PendingHobby(
                    id: null,
                    pendingUserId: $pendingUserInTable->getId(),
                    hobby: $hobby
                );
                $this->pendingHobbiesDAOImpl->create($pendingHobby);
            }

            return $pendingUserInTable;
        } catch (Throwable $t) {
            if (isset($profileImage)) {
                FileUtility::deleteImageWithThumbnail(
                    storeDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_UPLOAD'),
                    thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_THUMBNAIL'),
                    imageFileName: $profileImage
                );
            }
            if (isset($pendingUserInTable)) {
                $this->pendingUsersDAOImpl->delete($pendingUserInTable->getId());
            }
            throw $t;
        }
    }

    public function updateUserByPending(string $hash): User
    {
        $emailVerification = $this->emailVerificationDAOImpl->getByHash($hash);
        $pendingUserId = $emailVerification->getPendingUserId();
        $pendingUser = $this->pendingUsersDAOImpl->getById($pendingUserId);
        if (is_null($pendingUser)) {
            throw new InternalServerException("PendingUser doesn't exist");
        }
        try {
            $currentDatetime = date('Y-m-d H:i:s');
            $userId = $pendingUser->getUserId();
            $currentUser = $this->usersDAOImpl->getById($userId);
            if (!is_null($currentUser->getProfileImage())) {
                FileUtility::deleteImageWithThumbnail(
                    storeDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_UPLOAD'),
                    thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_THUMBNAIL'),
                    imageFileName: $currentUser->getProfileImage()
                );
            }
            $currentUser->setName($pendingUser->getName());
            $currentUser->setPasswordHash($pendingUser->getPasswordHash());
            $currentUser->setEmail($pendingUser->getEmail());
            $currentUser->setSelfIntroduction($pendingUser->getSelfIntroduction());
            $currentUser->setProfileImage($pendingUser->getProfileImage());
            $currentUser->setLastLogin($currentDatetime);
            $this->usersDAOImpl->update($currentUser);

            $pendingAddress = $this->pendingAddressesDAOImpl->getByUserId($pendingUser->getId());
            if (is_null($pendingAddress)) {
                throw new InternalServerException("PendingAddress doesn't exist");
            }
            $currentAddress = $this->addressesDAOImpl->getByUserId($userId);
            $currentAddress->setCountry($pendingAddress->getCountry());
            $currentAddress->setState($pendingAddress->getState());
            $currentAddress->setCity($pendingAddress->getCity());
            $currentAddress->setTown($pendingAddress->getTown());
            $this->addressesDAOImpl->update($currentAddress);

            $pendingCareers = $this->pendingCareersDAOImpl->getByUserId($pendingUser->getId());
            $this->careersDAOImpl->deleteByUserId($userId);
            foreach ($pendingCareers as $pendingCareer) {
                $career = new Career(
                    id: null,
                    userId: $userId,
                    job: $pendingCareer->getJob()
                );
                $this->careersDAOImpl->create($career);
            }

            $pendingHobbies = $this->pendingHobbiesDAOImpl->getByUserId($pendingUser->getId());
            $this->hobbiesDAOImpl->deleteByUserId($userId);
            foreach ($pendingHobbies as $pendingHobby) {
                $hobby = new Hobby(
                    id: null,
                    userId: $userId,
                    hobby: $pendingHobby->getHobby()
                );
                $this->hobbiesDAOImpl->create($hobby);
            }

            return $currentUser;
        } catch (Throwable $t) {
            throw $t;
        } finally {
            $this->pendingUsersDAOImpl->delete($pendingUser->getId());
            $this->emailVerificationDAOImpl->deleteByHash($hash);
        }
    }

    public function sendVerificationEmail(PendingUser $pendingUser, string $url): void
    {
        $url = Settings::env('FRONT_URL') . '/profile/validate_email?id=' . $url;
        $htmlBody = "Hello, " . $pendingUser->getName() . ".<br>";
        $htmlBody .= "Access the following URL to update profile.<br><a href=" . $url . ">Verification Link</a>";
        $textBody = "Hello, " . $pendingUser->getName() . "." . PHP_EOL;
        $textBody .= "Access the following URL to update profile." . PHP_EOL;
        $textBody .= $url;
        MailUtility::sendEmail(
            recipientEmail: $pendingUser->getEmail(),
            recipientName: $pendingUser->getName(),
            subject: 'Profile Update Email Verification',
            htmlBody: $htmlBody,
            textBody: $textBody
        );
    }

    public function getUserInfo(int $userId, bool $doMask): array
    {
        $user = $this->usersDAOImpl->getById($userId);
        if (is_null($user)) {
            return [];
        }
        if ($doMask) {
            $user->setPasswordHash("");
            $user->setEmail("");
            $user->setCreatedAt("");
            $user->setLastLogin("");
        }
        $address = $this->addressesDAOImpl->getByUserId($userId);
        $hobbies = $this->hobbiesDAOImpl->getByUserId($userId);
        $careers = $this->careersDAOImpl->getByUserId($userId);
        $profile = $user->toArray();
        $profile['address'] = $address->toArray();
        $profile['hobbies'] = [];
        foreach ($hobbies as $hobby) {
            array_push($profile['hobbies'], $hobby->getHobby());
        }
        $profile['careers'] = [];
        foreach ($careers as $career) {
            array_push($profile['careers'], $career->getJob());
        }
        $loginUserId = SessionManager::get('user_id');
        $loginUserFollows = $this->followsDAOImpl->getFollowers($loginUserId);
        $loginUserFollowers = [];
        foreach ($loginUserFollows as $follow) {
            array_push($loginUserFollowers, $follow->getFollowerId());
        }
        $loginUserFollow = $this->followsDAOImpl->getFollowees($loginUserId);
        $loginUserFollowees = [];
        foreach ($loginUserFollow as $follow) {
            array_push($loginUserFollowees, $follow->getFolloweeId());
        }
        $profile['isFollowedBy'] = in_array($userId, $loginUserFollowers);
        $profile['isFollowing'] = in_array($userId, $loginUserFollowees);
        return $profile;
    }

    public function deleteUser(int $userId): void
    {
        $this->usersDAOImpl->delete($userId);
        // TODO ユーザーに紐づく画像ファイルを削除する必要がある
        return;
    }
}
