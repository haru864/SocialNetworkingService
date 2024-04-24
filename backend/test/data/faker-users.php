<?php

use Database\DataAccess\Implementations\AddressesDAOImpl;
use Database\DataAccess\Implementations\CareersDAOImpl;
use Database\DataAccess\Implementations\FollowsDAOImpl;
use Database\DataAccess\Implementations\HobbiesDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
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
$usersCount = 59;

for ($i = 0; $i < $usersCount; $i++) {
    $nowDateTime = new DateTime();
    $nowDateTimeStr = $nowDateTime->format('Y-m-d H:i:s');
    $maxNameLength = 15;
    $username = $faker->regexify('[A-Za-z0-9]{1,' . $maxNameLength . '}');
    $user = new User(
        id: null,
        name: $username,
        password_hash: 'dummy_hash',
        email: $faker->email(),
        self_introduction: $faker->text(50),
        profile_image: null,
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



    // $imageFilePath = __DIR__ . '/images/random_image.png';
    // generateImage($imageFilePath);
    // $mimeType = getMimeType($imageFilePath);
    // $hashedFileName = preserveUploadedImageFile($postId, $nowDateTimeStr, $imageFilePath);
    // $post->setImageFileName($hashedFileName);
    // $post->setImageFileExtension($mimeType);
    // $postDAO->update($post);
}

function generateHobby($faker)
{
    do {
        $hobby = $faker->sentence($nbWords = 6, $variableNbWords = true); // 可変長の文章を生成
    } while (strlen($hobby) > 100);
    return $hobby;
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

function getMimeType(string $filePath): string
{
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath);
    finfo_close($finfo);
    return $mimeType;
}

function preserveUploadedImageFile(int $postId, string $createdAt, string $imageFilePath): string
{
    $stringToHash = $postId . $createdAt . $imageFilePath;
    $hashedFileName = hash('sha256', $stringToHash);
    $storagedFilePath = Settings::env('UPLOADED_IMAGE_FILE_LOCATION') . '/' . $hashedFileName;
    rename($imageFilePath, $storagedFilePath);
    createThumbnail($storagedFilePath);
    return $hashedFileName;
}

function createThumbnail(string $imageFilePath, int $thumbWidth = 150): string
{
    $image = new \Imagick($imageFilePath);
    $width = $image->getImageWidth();
    $height = $image->getImageHeight();
    $aspectRatio = $height / $width;
    $thumbHeight = $thumbWidth * $aspectRatio;
    $image->resizeImage($thumbWidth, $thumbHeight, \Imagick::FILTER_LANCZOS, 1);
    $thumbnailFile = Settings::env('THUMBNAIL_FILE_LOCATION') . '/' . basename($imageFilePath);
    $image->writeImage($thumbnailFile);
    $image->clear();
    $image->destroy();
    return $thumbnailFile;
}
