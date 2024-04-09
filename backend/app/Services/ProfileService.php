<?php

namespace Services;

use Database\DataAccess\Implementations\AddressesDAOImpl;
use Database\DataAccess\Implementations\CareersDAOImpl;
use Database\DataAccess\Implementations\HobbiesDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Helpers\FileUtility;
use Helpers\ValidationHelper;
use Http\Request\PostProfileRequest;
use Models\Address;
use Models\Career;
use Models\Hobby;
use Models\User;
use Settings\Settings;
use Throwable;

class ProfileService
{
    private UsersDAOImpl $usersDAOImpl;
    private AddressesDAOImpl $addressesDAOImpl;
    private CareersDAOImpl $careersDAOImpl;
    private HobbiesDAOImpl $hobbiesDAOImpl;

    public function __construct(
        usersDAOImpl $usersDAOImpl,
        AddressesDAOImpl $addressesDAOImpl,
        CareersDAOImpl $careersDAOImpl,
        HobbiesDAOImpl $hobbiesDAOImpl
    ) {
        $this->usersDAOImpl = $usersDAOImpl;
        $this->addressesDAOImpl = $addressesDAOImpl;
        $this->careersDAOImpl = $careersDAOImpl;
        $this->hobbiesDAOImpl = $hobbiesDAOImpl;
    }

    // TODO 更新回数によってはidが枯渇するので、idカラムの削除などを検討する
    // TODO メールアドレスを誤って変更した場合に認証ができずに詰むため、メール認証が完了するまで変更を反映しないようにする
    public function updateUser(PostProfileRequest $request, int $userId): User
    {
        $currentUser = $this->usersDAOImpl->getById($userId);
        $currentAddress = $this->addressesDAOImpl->getByUserId($userId);
        $currentCareers = $this->careersDAOImpl->getByUserId($userId);
        $currentHobbies = $this->hobbiesDAOImpl->getByUserId($userId);
        if (is_null($currentUser)) {
            throw new InvalidRequestParameterException("Specified user does not exist.");
        }
        if (
            $request->getUsername() !== $currentUser->getName()
            && $this->usersDAOImpl->getByName($request->getUsername()) !== null
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
            $currentEmail = $currentUser->getEmail();
            $updatedEmail = $request->getEmail();
            $isEmailChanged = $currentEmail !== $updatedEmail;
            $updatedUser = new User(
                id: $userId,
                name: $request->getUsername(),
                password_hash: password_hash($request->getPassword(), PASSWORD_DEFAULT),
                email: $request->getEmail(),
                self_introduction: ValidationHelper::isNonEmptyString($request->getSelfIntroduction()) ? $request->getSelfIntroduction() : null,
                profile_image: $profileImage,
                created_at: $currentUser->getCreatedAt(),
                last_login: date('Y-m-d H:i:s'),
                email_verified_at: $isEmailChanged ? null : $currentUser->getEmailVerifiedAt()
            );
            $this->usersDAOImpl->update($updatedUser);

            $updatedAddress = new Address(
                id: $currentAddress->getId(),
                userId: $currentAddress->getUserId(),
                country: $request->getCountry(),
                state: $request->getState(),
                city: $request->getCity(),
                town: $request->getTown()
            );
            $this->addressesDAOImpl->update($updatedAddress);

            foreach ($currentCareers as $career) {
                $this->careersDAOImpl->delete($career->getId());
            }
            foreach ($request->getCareers() as $job) {
                $updatedCareer = new Career(
                    id: null,
                    userId: $userId,
                    job: $job
                );
                $this->careersDAOImpl->create($updatedCareer);
            }

            foreach ($currentHobbies as $hobby) {
                $this->hobbiesDAOImpl->delete($hobby->getId());
            }
            foreach ($request->getHobbies() as $hobby) {
                $updatedHobby = new Hobby(
                    id: null,
                    userId: $userId,
                    hobby: $hobby
                );
                $this->hobbiesDAOImpl->create($updatedHobby);
            }
            return $updatedUser;
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
        } finally {
            if (!is_null($currentUser->getProfileImage())) {
                FileUtility::deleteImageWithThumbnail(
                    storeDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_UPLOAD'),
                    thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_THUMBNAIL'),
                    imageFileName: $currentUser->getProfileImage()
                );
            }
        }
    }

    public function getUserInfo(int $userId): array
    {
        $user = $this->usersDAOImpl->getById($userId);
        if (is_null($user)) {
            return [];
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
        return $profile;
    }

    public function deleteUser(int $userId): void
    {
        $this->usersDAOImpl->delete($userId);
        return;
    }
}
