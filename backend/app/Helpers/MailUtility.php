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

        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = Settings::env('SMTP_SERVER_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = Settings::env('SMTP_SERVER_USERNAME');
        $mail->Password = Settings::env('SMTP_SERVER_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = Settings::env('SMTP_SERVER_PORT');

        $mail->setFrom(Settings::env('SMTP_SERVER_USERNAME'), 'SNS Service Mailer');
        $mail->addAddress($recipientEmail, $recipientName);

        $mailCharSet = 'ISO-2022-JP';
        $mail->CharSet = $mailCharSet;
        $mail->isHTML(true);
        $mail->Subject = mb_encode_mimeheader($subject, $mailCharSet);
        $mail->Body = $htmlBody;
        $mail->AltBody = $textBody;

        $mail->send();
    }

    public static function deleteOldMail(int $retentionDays = 30, string $subject = ''): void
    {
        $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
        $username = Settings::env('SMTP_SERVER_USERNAME');
        $password = Settings::env('SMTP_SERVER_PASSWORD');

        $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

        $date = date('d-M-Y', strtotime('-' . $retentionDays . ' days'));
        $emails = imap_search($inbox, 'BEFORE ' . $date);

        if ($emails) {
            foreach ($emails as $email_number) {
                $header = imap_headerinfo($inbox, $email_number);
                $mailSubject = mb_decode_mimeheader($header->subject);
                if (strpos($mailSubject, $subject) !== false) {
                    imap_delete($inbox, $email_number);
                }
            }

            imap_expunge($inbox);
        }

        imap_close($inbox);
    }

    public static function checkGmailFolderList(): void
    {
        $hostname = '{imap.gmail.com:993/imap/ssl}';
        $username = Settings::env('SMTP_SERVER_USERNAME');
        $password = Settings::env('SMTP_SERVER_PASSWORD');

        $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

        $folders = imap_list($inbox, $hostname, '*');

        if ($folders) {
            foreach ($folders as $folder) {
                echo $folder . PHP_EOL;
            }
        } else {
            echo "Could not retrieve folders.\n";
        }

        imap_close($inbox);
    }
}
