<?php

spl_autoload_extensions(".php");
spl_autoload_register(function ($class) {
    $class = str_replace("\\", "/", $class);
    $file = __DIR__ . "/../" . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Helpers\MailUtility;
use Logging\Logger;
use Settings\Settings;

date_default_timezone_set('Asia/Tokyo');
$logger = Logger::getInstance();

$SUBJECT_SIGNUP_VERIFICATION_EMAIL = '[SNS] Sign-Up Email Verification';
$SUBJECT_PROFILE_UPDATE_VERIFICATION_EMAIL = '[SNS] Profile Update Email Verification';

try {
    $logger->logInfo('[Mail Deletion] Start Execution.');
    MailUtility::deleteOldMail(
        retentionDays: Settings::env('MAIL_RETENTION_DAYS'),
        subject: $SUBJECT_SIGNUP_VERIFICATION_EMAIL
    );
    MailUtility::deleteOldMail(
        retentionDays: Settings::env('MAIL_RETENTION_DAYS'),
        subject: $SUBJECT_PROFILE_UPDATE_VERIFICATION_EMAIL
    );
    $logger->logInfo('[Mail Deletion] Finish successfully.');
} catch (Throwable $t) {
    $logger->logInfo('[Mail Deletion] Terminate with error.');
    $logger->logError($t);
}
