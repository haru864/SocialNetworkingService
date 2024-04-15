<?php

namespace Services;

use Database\DataAccess\Implementations\EmailVerificationDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InternalServerException;
use Exceptions\InvalidRequestParameterException;
use Helpers\MailUtility;
use Models\User;
use Models\EmailVerification;
use Settings\Settings;

class ResetPasswordService
{
    private UsersDAOImpl $usersDAOImpl;
    private EmailVerificationDAOImpl $emailVerificationDAOImpl;

    public function __construct(
        usersDAOImpl $usersDAOImpl,
        EmailVerificationDAOImpl $emailVerificationDAOImpl
    ) {
        $this->usersDAOImpl = $usersDAOImpl;
        $this->emailVerificationDAOImpl = $emailVerificationDAOImpl;
    }

    public function sendEmail(string $username, string $email): void
    {
        $userInTable = $this->usersDAOImpl->getByName($username);
        if (is_null($userInTable)) {
            throw new InvalidRequestParameterException("Specified username does not exist.");
        }
        if ($userInTable->getEmail() !== $email) {
            throw new InvalidRequestParameterException("Specified email is not correct.");
        }
        $url = Settings::env('FRONT_URL') . '/reset_password/reset?id=' . $this->publishURL($userInTable);
        $htmlBody = "<a href=" . $url . ">Password reset link</a>";
        $textBody = "Access the following URL to reset your password." . PHP_EOL . $url;
        MailUtility::sendEmail(
            recipientEmail: $email,
            recipientName: $username,
            subject: 'Reset your password',
            htmlBody: $htmlBody,
            textBody: $textBody
        );
        return;
    }

    public function resetPassword(string $newPassword, string $hash): void
    {
        $this->emailVerificationDAOImpl->deleteExpiredHash();
        $emailVerification = $this->emailVerificationDAOImpl->getByHash($hash);
        if (is_null($emailVerification)) {
            throw new InvalidRequestParameterException('Invalid URL hash or URL expired.');
        }
        $userId = $emailVerification->getUserId();
        $user = $this->usersDAOImpl->getById($userId);
        if (is_null($user)) {
            throw new InvalidRequestParameterException('Given URL hash has no related account.');
        }
        $user->setPasswordHash(password_hash($newPassword, PASSWORD_DEFAULT));
        $this->usersDAOImpl->update($user);
        $this->emailVerificationDAOImpl->deleteByHash($hash);
        return;
    }

    private function publishURL(User $user): string
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
