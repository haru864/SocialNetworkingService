<?php

use Database\DataAccess\Implementations\AddressesDAOImpl;
use Database\DataAccess\Implementations\CareersDAOImpl;
use Database\DataAccess\Implementations\FollowsDAOImpl;
use Database\DataAccess\Implementations\HobbiesDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Helpers\FileUtility;
use Models\Address;
use Models\Career;
use Models\Follow;
use Models\Hobby;
use Models\User;
use Settings\Settings;

require_once __DIR__ . '/../../vendor/autoload.php';

spl_autoload_extensions(".php");
spl_autoload_register(function ($class) {
    $class = str_replace("\\", "/", $class);
    $file = __DIR__ . "/../../app/" . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

date_default_timezone_set('Asia/Tokyo');
$faker = Faker\Factory::create();
$usersDAOImpl = new UsersDAOImpl();
$addressesDAOImpl = new AddressesDAOImpl();
$hobbiesDAOImpl = new HobbiesDAOImpl();
$careersDAOImpl = new CareersDAOImpl();
$followsDAOImpl = new FollowsDAOImpl();
$usersCount = 25;

for ($i = 0; $i < $usersCount; $i++) {
    $nowDateTime = new DateTime();
    $nowDateTimeStr = $nowDateTime->format('Y-m-d H:i:s');
    $maxNameLength = 15;
    $username = $faker->regexify('[A-Za-z0-9]{1,' . $maxNameLength . '}');
    $imagePath = __DIR__ . '/images/random_image.png';
    generateImage($imagePath);
    $profileImage = storeImageWithThumbnail(
        storeDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_UPLOAD'),
        thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_PROFILE_THUMBNAIL'),
        uploadedTmpFilePath: $imagePath,
        uploadedFileName: 'random_image.png',
        thumbWidth: 100
    );
    $user = new User(
        id: null,
        name: $username,
        password_hash: 'dummy_hash',
        email: $faker->email(),
        self_introduction: $faker->text(50),
        profile_image: $profileImage,
        created_at: $nowDateTimeStr,
        last_login: $nowDateTimeStr
    );
    $userInTable = $usersDAOImpl->create($user);
    $userId = $userInTable->getId();

    $address = new Address(
        id: null,
        userId: $userId,
        country: $faker->country(),
        state: $faker->state(),
        city: $faker->city(),
        town: $faker->city()
    );
    $addressesDAOImpl->create($address);

    $hobbiesCount = $faker->numberBetween(0, 3);
    for ($j = 0; $j < $hobbiesCount; $j++) {
        $hobby = new Hobby(null, $userId, generateHobby($faker));
        $hobbiesDAOImpl->create($hobby);
    }

    $careersCount = $faker->numberBetween(0, 3);
    for ($j = 0; $j < $careersCount; $j++) {
        $career = new Career(null, $userId, $faker->jobTitle);
        $careersDAOImpl->create($career);
    }

    $follow = new Follow(null, $userId, 3, $nowDateTimeStr);
    $followsDAOImpl->create($follow);
}

function generateHobby($faker)
{
    do {
        $hobby = $faker->sentence($nbWords = 6, $variableNbWords = true); // 可変長の文章を生成
    } while (strlen($hobby) > 100);
    return $hobby;
}

function storeImageWithThumbnail(
    string $storeDirPath,
    string $thumbDirPath,
    string $uploadedTmpFilePath,
    string $uploadedFileName,
    int $thumbWidth
): string {
    $hash = generateUniqueHashWithLimit($storeDirPath, $uploadedFileName);
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($uploadedTmpFilePath);
    $validImageMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif'
    ];
    if (!array_key_exists($mimeType, $validImageMimeTypes)) {
        throw new Exception("Invalid Mime-Type: '{$mimeType}'");
    }
    $imageFileExtension = $validImageMimeTypes[$mimeType];
    $storedImageFileName = $hash . '.' . $imageFileExtension;
    $storedImageFilePath = $storeDirPath . DIRECTORY_SEPARATOR . $storedImageFileName;
    if (!rename($uploadedTmpFilePath, $storedImageFilePath)) {
        throw new Exception("Failed to move uploaded file. ({$uploadedTmpFilePath} => {$storedImageFilePath})");
    }
    createThumbnail($storedImageFilePath, $thumbDirPath, $thumbWidth);
    return $storedImageFileName;
}

function generateImage(string $filePath): void
{
    $width = rand(100, 1000);
    $height = rand(100, 1000);
    $image = imagecreatetruecolor($width, $height);
    $backgroundColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    imagefill($image, 0, 0, $backgroundColor);
    $lineColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    imagerectangle($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
    imagepng($image, $filePath);
    imagedestroy($image);
}

function generateUniqueHashWithLimit(string $dirPath, string $data, $limit = 100): string
{
    $iterator = new \DirectoryIterator($dirPath);
    $hash = hash('sha256', $data);
    $counter = 0;
    while ($counter < $limit) {
        $unique = true;
        foreach ($iterator as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $filenameWithoutExtension = $fileinfo->getBasename('.' . $fileinfo->getExtension());
            }
            if ($hash === $filenameWithoutExtension) {
                $unique = false;
            }
        }
        if ($unique) {
            return $hash;
        }
        $counter++;
        $hash = hash('sha256', $data . $counter);
    }
    throw new Exception('Failed to generate unique hash value.');
}

function createThumbnail(string $imageFilePath, string $thumbDirPath, int $thumbWidth = 100): void
{
    $image = new \Imagick($imageFilePath);
    $width = $image->getImageWidth();
    $height = $image->getImageHeight();
    $aspectRatio = $height / $width;
    $thumbHeight = $thumbWidth * $aspectRatio;
    $image->resizeImage($thumbWidth, $thumbHeight, \Imagick::FILTER_LANCZOS, 1);
    $thumbnailFile = $thumbDirPath . DIRECTORY_SEPARATOR . basename($imageFilePath);
    $image->writeImage($thumbnailFile);
    $image->clear();
    $image->destroy();
    return;
}
