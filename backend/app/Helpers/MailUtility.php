<?php

namespace Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Settings\Settings;

class MailUtility
{
    public static function sendEmail(
        string $recipientEmail,
        string $recipientName,
        string $subject,
        string $htmlBody,
        string $textBody
    ): void {
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
        $mail->setFrom(Settings::env('SMTP_SERVER_USERNAME'), 'SNS Service Mailer');
        $mail->addAddress($recipientEmail, $recipientName);

        // Content
        $mailCharSet = 'ISO-2022-JP';
        $mail->CharSet = $mailCharSet;
        $mail->isHTML(true);
        $mail->Subject = mb_encode_mimeheader($subject, $mailCharSet);
        $mail->Body = $htmlBody;
        $mail->AltBody = $textBody;

        $mail->send();
    }

    // TODO 送信済みメールをボックスから削除する
    public static function deleteOldMail(): void
    {
    }
}
