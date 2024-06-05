<?php

namespace Services;

use Database\DataAccess\Implementations\AddressesDAOImpl;
use Database\DataAccess\Implementations\CareersDAOImpl;
use Database\DataAccess\Implementations\EmailVerificationDAOImpl;
use Database\DataAccess\Implementations\HobbiesDAOImpl;
use Database\DataAccess\Implementations\PendingAddressesDAOImpl;
use Database\DataAccess\Implementations\PendingCareersDAOImpl;
use Database\DataAccess\Implementations\PendingHobbiesDAOImpl;
use Database\DataAccess\Implementations\PendingUsersDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InternalServerException;
use Exceptions\InvalidRequestParameterException;
use Exceptions\InvalidRequestURIException;
use Helpers\FileUtility;
use Helpers\MailUtility;
use Http\Request\SignupRequest;
use Models\User;
use Helpers\ValidationHelper;
use Models\Address;
use Models\Career;
use Models\EmailVerification;
use Models\Hobby;
use Models\PendingAddress;
use Models\PendingCareer;
use Models\PendingHobby;
use Models\PendingUser;
use Settings\Settings;
use Throwable;

class SignupService
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

    public function __construct(
        usersDAOImpl $usersDAOImpl,
        AddressesDAOImpl $addressesDAOImpl,
        CareersDAOImpl $careersDAOImpl,
        HobbiesDAOImpl $hobbiesDAOImpl,
        PendingUsersDAOImpl $pendingUsersDAOImpl,
        PendingAddressesDAOImpl $pendingAddressesDAOImpl,
        PendingCareersDAOImpl $pendingCareersDAOImpl,
        PendingHobbiesDAOImpl $pendingHobbiesDAOImpl,
        EmailVerificationDAOImpl $emailVerificationDAOImpl
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
    }

    public function createPendingUser(SignupRequest $request): PendingUser
    {
        if (
            $this->usersDAOImpl->getByName($request->getUsername()) !== null
            || $this->pendingUsersDAOImpl->getByName($request->getUsername()) !== null
        ) {
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
                userId: null,
                name: $request->getUsername(),
                password_hash: password_hash($request->getPassword(), PASSWORD_DEFAULT),
                email: $request->getEmail(),
                self_introduction: ValidationHelper::isNonEmptyString($request->getSelfIntroduction()) ? $request->getSelfIntroduction() : null,
                profile_image: $profileImage,
            );
            $pendingUserInTable = null;
            $pendingUserInTable = $this->pendingUsersDAOImpl->create($pendingUser);

            $pendingAddress = new PendingAddress(
                id: null,
                pendingUserId: $pendingUserInTable->getId(),
                country: $request->getCountry(),
                state: $request->getState(),
                city: $request->getCity(),
                town: $request->getTown()
            );
            $this->pendingAddressesDAOImpl->create($pendingAddress);

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

    public function createUserByPending(string $hash): User
    {
        $emailVerification = $this->emailVerificationDAOImpl->getByHash($hash);
        $pendingUserId = $emailVerification->getPendingUserId();
        $pendingUser = $this->pendingUsersDAOImpl->getById($pendingUserId);
        if (is_null($pendingUser)) {
            throw new InternalServerException("PendingUser doesn't exist");
        }
        try {
            $currentDatetime = date('Y-m-d H:i:s');
            $user = new User(
                id: null,
                name: $pendingUser->getName(),
                password_hash: $pendingUser->getPasswordHash(),
                email: $pendingUser->getEmail(),
                self_introduction: $pendingUser->getSelfIntroduction(),
                profile_image: $pendingUser->getProfileImage(),
                created_at: $currentDatetime,
                last_login: $currentDatetime
            );
            $userInTable = null;
            $userInTable = $this->usersDAOImpl->create($user);

            $pendingAddress = $this->pendingAddressesDAOImpl->getByUserId($pendingUser->getId());
            if (is_null($pendingAddress)) {
                throw new InternalServerException("PendingAddress doesn't exist");
            }
            $address = new Address(
                id: null,
                userId: $userInTable->getId(),
                country: $pendingAddress->getCountry(),
                state: $pendingAddress->getState(),
                city: $pendingAddress->getCity(),
                town: $pendingAddress->getTown()
            );
            $this->addressesDAOImpl->create($address);

            $pendingCareers = $this->pendingCareersDAOImpl->getByUserId($pendingUser->getId());
            foreach ($pendingCareers as $pendingCareer) {
                $career = new Career(
                    id: null,
                    userId: $userInTable->getId(),
                    job: $pendingCareer->getJob()
                );
                $this->careersDAOImpl->create($career);
            }

            $pendingHobbies = $this->pendingHobbiesDAOImpl->getByUserId($pendingUser->getId());
            foreach ($pendingHobbies as $pendingHobby) {
                $hobby = new Hobby(
                    id: null,
                    userId: $userInTable->getId(),
                    hobby: $pendingHobby->getHobby()
                );
                $this->hobbiesDAOImpl->create($hobby);
            }

            return $userInTable;
        } catch (Throwable $t) {
            if (!is_null($pendingUser->getProfileImage())) {
                FileUtility::deleteImageWithThumbnail(
                    storeDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_UPLOAD'),
                    thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_THUMBNAIL'),
                    imageFileName: $pendingUser->getProfileImage()
                );
            }
            if (!is_null($userInTable)) {
                $this->usersDAOImpl->delete($userInTable->getId());
            }
            throw $t;
        } finally {
            $this->pendingUsersDAOImpl->delete($pendingUser->getId());
            $this->emailVerificationDAOImpl->deleteByHash($hash);
        }
    }

    public function sendVerificationEmail(PendingUser $pendingUser): void
    {
        $url = Settings::env('FRONT_URL') . '/signup/validate_email?id=' . $this->publishUserVerificationURL($pendingUser);
        $htmlBody = "Hello, " . $pendingUser->getName() . ".<br>";
        $htmlBody .= "Access the following URL to complete Sign-Up.<br><a href=" . $url . ">Verification Link</a>";
        $textBody = "Hello, " . $pendingUser->getName() . "." . PHP_EOL;
        $textBody .= "Access the following URL to complete Sign-Up." . PHP_EOL;
        $textBody .= $url;
        MailUtility::sendEmail(
            recipientEmail: $pendingUser->getEmail(),
            recipientName: $pendingUser->getName(),
            subject: 'Sign-Up Email Verification',
            htmlBody: $htmlBody,
            textBody: $textBody
        );
    }

    public function validateEmail(string $hash): void
    {
        $this->emailVerificationDAOImpl->deleteExpiredHash();
        $emailVerification = $this->emailVerificationDAOImpl->getByHash($hash);
        if (is_null($emailVerification)) {
            throw new InvalidRequestURIException('Invalid Verification URL.');
        }
        $pendingUserId = $emailVerification->getPendingUserId();
        $pendingUser = $this->pendingUsersDAOImpl->getById($pendingUserId);
        if (is_null($pendingUser)) {
            throw new InvalidRequestURIException('Given URL has no related account.');
        }
    }

    public function publishUserVerificationURL(PendingUser $pendingUser): string
    {
        try {
            $this->emailVerificationDAOImpl->deleteExpiredHash();
            $hash = hash('sha256', $pendingUser->getId());
            $counter = 0;
            while ($counter < 1000) {
                $urlInTable = $this->emailVerificationDAOImpl->getByHash($hash);
                if (is_null($urlInTable)) {
                    $createdAt = date('Y-m-d H:i:s');
                    $expiredAt = new \Datetime('NOW');
                    $expiredAt->add(\DateInterval::createFromDateString('10 minutes'));
                    $emailVerification = new EmailVerification(
                        hash: $hash,
                        pendingUserId: $pendingUser->getId(),
                        createdAt: $createdAt,
                        expiredAt: $expiredAt->format("Y-m-d H:i:s")
                    );
                    $this->emailVerificationDAOImpl->create($emailVerification);
                    return $hash;
                }
                $counter++;
                $hash = hash('sha256',  $pendingUser->getId() . $counter);
            }
            throw new InternalServerException('Failed to generate unique hash value.');
        } catch (Throwable $t) {
            if (!is_null($pendingUser->getProfileImage())) {
                FileUtility::deleteImageWithThumbnail(
                    storeDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_UPLOAD'),
                    thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_THUMBNAIL'),
                    imageFileName: $pendingUser->getProfileImage()
                );
            }
            $this->pendingUsersDAOImpl->delete($pendingUser->getId());
            throw $t;
        }
    }
}
