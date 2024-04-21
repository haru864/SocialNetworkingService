<?php

namespace Services;

use Database\DataAccess\Implementations\AddressesDAOImpl;
use Database\DataAccess\Implementations\CareersDAOImpl;
use Database\DataAccess\Implementations\EmailVerificationDAOImpl;
use Database\DataAccess\Implementations\HobbiesDAOImpl;
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
use Settings\Settings;
use Throwable;

class SignupService
{
    private UsersDAOImpl $usersDAOImpl;
    private AddressesDAOImpl $addressesDAOImpl;
    private CareersDAOImpl $careersDAOImpl;
    private HobbiesDAOImpl $hobbiesDAOImpl;
    private EmailVerificationDAOImpl $emailVerificationDAOImpl;

    public function __construct(
        usersDAOImpl $usersDAOImpl,
        AddressesDAOImpl $addressesDAOImpl,
        CareersDAOImpl $careersDAOImpl,
        HobbiesDAOImpl $hobbiesDAOImpl,
        EmailVerificationDAOImpl $emailVerificationDAOImpl
    ) {
        $this->usersDAOImpl = $usersDAOImpl;
        $this->addressesDAOImpl = $addressesDAOImpl;
        $this->careersDAOImpl = $careersDAOImpl;
        $this->hobbiesDAOImpl = $hobbiesDAOImpl;
        $this->emailVerificationDAOImpl = $emailVerificationDAOImpl;
    }

    public function createUser(SignupRequest $request): User
    {
        if ($this->usersDAOImpl->getByName($request->getUsername()) != null) {
            throw new InvalidRequestParameterException("Specified username is already used.");
        }
        try {
            $currentDatetime = date('Y-m-d H:i:s');
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
            $user = new User(
                id: null,
                name: $request->getUsername(),
                password_hash: password_hash($request->getPassword(), PASSWORD_DEFAULT),
                email: $request->getEmail(),
                self_introduction: ValidationHelper::isNonEmptyString($request->getSelfIntroduction()) ? $request->getSelfIntroduction() : null,
                profile_image: $profileImage,
                created_at: $currentDatetime,
                last_login: $currentDatetime,
                email_verified_at: null
            );
            $userInTable = null;
            $userInTable = $this->usersDAOImpl->create($user);

            if (
                $request->getCountry() !== ''
                || $request->getState() !== ''
                || $request->getCity() !== ''
                || $request->getTown() !== ''
            ) {
                $address = new Address(
                    id: null,
                    userId: $userInTable->getId(),
                    country: $request->getCountry(),
                    state: $request->getState(),
                    city: $request->getCity(),
                    town: $request->getTown()
                );
                $this->addressesDAOImpl->create($address);
            }

            foreach ($request->getCareers() as $job) {
                $career = new Career(
                    id: null,
                    userId: $userInTable->getId(),
                    job: $job
                );
                $this->careersDAOImpl->create($career);
            }

            foreach ($request->getHobbies() as $hobby) {
                $hobbyObj = new Hobby(
                    id: null,
                    userId: $userInTable->getId(),
                    hobby: $hobby
                );
                $this->hobbiesDAOImpl->create($hobbyObj);
            }

            return $userInTable;
        } catch (Throwable $t) {
            if (isset($profileImage)) {
                FileUtility::deleteImageWithThumbnail(
                    storeDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_UPLOAD'),
                    thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_THUMBNAIL'),
                    imageFileName: $profileImage
                );
            }
            if (isset($userInTable)) {
                $this->usersDAOImpl->delete($userInTable->getId());
            }
            throw $t;
        }
    }

    public function sendVerificationEmail(User $user): void
    {
        $url = Settings::env('FRONT_URL') . '/validate_email?id=' . $this->publishUserVerificationURL($user);
        $htmlBody = "Hello, " . $user->getName() . ".<br>" . "Access the following URL to complete Sign-Up.<br><a href=" . $url . ">Verification Link</a>";
        $textBody = "Hello, " . $user->getName() . "." . PHP_EOL . "Access the following URL to complete Sign-Up." . PHP_EOL . $url;
        MailUtility::sendEmail(
            recipientEmail: $user->getEmail(),
            recipientName: $user->getName(),
            subject: 'Sign-Up Email Verification',
            htmlBody: $htmlBody,
            textBody: $textBody
        );
    }

    public function validateEmail(string $hash): User
    {
        $this->emailVerificationDAOImpl->deleteExpiredHash();
        $emailVerification = $this->emailVerificationDAOImpl->getByHash($hash);
        if (is_null($emailVerification)) {
            throw new InvalidRequestURIException('Invalid Verification URL.');
        }
        $userId = $emailVerification->getUserId();
        $user = $this->usersDAOImpl->getById($userId);
        if (is_null($user)) {
            throw new InvalidRequestURIException('Given URL has no related account.');
        }
        $currentDatetime = date('Y-m-d H:i:s');
        $user->setEmailVerifiedAt($currentDatetime);
        $this->usersDAOImpl->update($user);
        $this->emailVerificationDAOImpl->deleteByHash($hash);
        return $user;
    }

    private function publishUserVerificationURL(User $user): string
    {
        $this->emailVerificationDAOImpl->deleteExpiredHash();
        $hash = hash('sha256', $user->getId());
        $counter = 0;
        while ($counter < 1000) {
            $urlInTable = $this->emailVerificationDAOImpl->getByHash($hash);
            if (is_null($urlInTable)) {
                $createdAt = date('Y-m-d H:i:s');
                $expiredAt = new \Datetime('NOW');
                $expiredAt->add(\DateInterval::createFromDateString('10 minutes'));
                $emailVerification = new EmailVerification(
                    hash: $hash,
                    userId: $user->getId(),
                    createdAt: $createdAt,
                    expiredAt: $expiredAt->format("Y-m-d H:i:s")
                );
                $this->emailVerificationDAOImpl->create($emailVerification);
                return $hash;
            }
            $counter++;
            $hash = hash('sha256',  $user->getId() . $counter);
        }
        throw new InternalServerException('Failed to generate unique hash value.');
    }
}
