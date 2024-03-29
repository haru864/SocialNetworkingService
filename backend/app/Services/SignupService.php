<?php

namespace Services;

use Database\DataAccess\Implementations\AddressesDAOImpl;
use Database\DataAccess\Implementations\CareersDAOImpl;
use Database\DataAccess\Implementations\HobbiesDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Helpers\FileUtility;
use Http\Request\SignupRequest;
use Models\User;
use Helpers\ValidationHelper;
use Models\Address;
use Models\Career;
use Models\Hobby;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Settings\Settings;

class SignupService
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

    // TODO DB登録に失敗した場合、保存された画像ファイルを削除する必要がある。
    public function createUser(SignupRequest $request): User
    {
        $currentDatetime = date('Y-m-d H:i:s');
        $user = new User(
            id: null,
            name: $request->getUsername(),
            password_hash: password_hash($request->getPassword(), PASSWORD_DEFAULT),
            email: $request->getEmail(),
            self_introduction: ValidationHelper::isNonEmptyString($request->getSelfIntroduction()) ? $request->getSelfIntroduction() : null,
            profile_image: FileUtility::storeImageWithThumbnail(
                storeDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_UPLOAD'),
                thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_THUMBNAIL'),
                uploadedTmpFilePath: $request->getProfileImage()['tmp_name'],
                uploadedFileName: $request->getProfileImage()['name'],
                thumbWidth: 150
            ),
            created_at: $currentDatetime,
            last_login: $currentDatetime,
            email_verified_at: null
        );
        $userInTable = $this->usersDAOImpl->create($user);
        $address = new Address(
            id: null,
            userId: $userInTable->getId(),
            country: $request->getCountry(),
            state: $request->getState(),
            city: $request->getCity(),
            town: $request->getTown()
        );
        $this->addressesDAOImpl->create($address);
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
    }

    // TODO 実行時にクライアントにログが返らないようにする
    public function sendVerificationEmail(User $user): void
    {
        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = Settings::env('SMTP_SERVER_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = Settings::env('SMTP_SERVER_USERNAME');
        $mail->Password = Settings::env('SMTP_SERVER_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = Settings::env('SMTP_SERVER_PORT');

        //Recipients
        $mail->setFrom(Settings::env('SMTP_SERVER_USERNAME'), 'Mailer');
        $mail->addAddress($user->getEmail(), 'Joe User');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Here is the subject';
        $mail->Body = 'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
    }

    private function publishUserVerificationURL(): string
    {
        $url = '';
        return $url;
    }
}
