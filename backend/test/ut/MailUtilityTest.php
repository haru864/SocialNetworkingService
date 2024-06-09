<?php

$APP_DIRECTORY = __DIR__ . "/../../app/";
require __DIR__ . '/../../vendor/autoload.php';

spl_autoload_extensions(".php");

spl_autoload_register(function ($class)  use ($APP_DIRECTORY) {
    $class = str_replace("\\", "/", $class);
    $file = $APP_DIRECTORY . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Helpers\MailUtility;
use PHPUnit\Framework\TestCase;


class MailUtilityTest extends TestCase
{
    public function test_deleteOldMail()
    {
        if (function_exists('imap_open')) {
            echo "IMAP extension is enabled." . PHP_EOL;
        } else {
            echo "IMAP extension is not enabled." . PHP_EOL;
        }

        MailUtility::deleteOldMail(
            retentionDays: 10,
            subject: '[SNS] Profile Update Email Verification'
        );
        MailUtility::deleteOldMail(
            retentionDays: 10,
            subject: '[SNS] Sign-Up Email Verification'
        );

        // MailUtility::checkGmailFolderList();
    }
}
