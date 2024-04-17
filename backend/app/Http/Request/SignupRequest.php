<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class SignupRequest
{
    private string $username;
    private string $password;
    private string $email;
    private string $selfIntroduction;
    private ?array $profileImage;  // $_FILESオブジェクト
    private string $country;
    private string $state;
    private string $city;
    private string $town;
    private string $hobby_1;
    private string $hobby_2;
    private string $hobby_3;
    private string $career_1;
    private string $career_2;
    private string $career_3;

    public function __construct($postData, $fileData)
    {
        $requiredParams = [
            'username' => ['type' => 'text', 'minCharCount' => 1, 'maxCharCount' => 15],
            'password' => ['type' => 'text', 'minCharCount' => 8, 'maxCharCount' => null],
            'email' => ['type' => 'text', 'minCharCount' => null, 'maxCharCount' => null],
            'self_introduction' => ['type' => 'text', 'minCharCount' => null, 'maxCharCount' => 50],
            // 'profile_image' => 'file',
            'country' => ['type' => 'text', 'minCharCount' => null, 'maxCharCount' => 100],
            'state' => ['type' => 'text', 'minCharCount' => null, 'maxCharCount' => 100],
            'city' => ['type' => 'text', 'minCharCount' => null, 'maxCharCount' => 100],
            'town' => ['type' => 'text', 'minCharCount' => null, 'maxCharCount' => 100],
            'hobby_1' => ['type' => 'text', 'minCharCount' => null, 'maxCharCount' => 100],
            'hobby_2' => ['type' => 'text', 'minCharCount' => null, 'maxCharCount' => 100],
            'hobby_3' => ['type' => 'text', 'minCharCount' => null, 'maxCharCount' => 100],
            'career_1' => ['type' => 'text', 'minCharCount' => null, 'maxCharCount' => 100],
            'career_2' => ['type' => 'text', 'minCharCount' => null, 'maxCharCount' => 100],
            'career_3' => ['type' => 'text', 'minCharCount' => null, 'maxCharCount' => 100],
        ];
        foreach ($requiredParams as $param => $paramInfo) {
            $type = $paramInfo['type'];
            $minCharCount = $paramInfo['minCharCount'];
            $maxCharCount = $paramInfo['maxCharCount'];
            $isNull = false;
            $isUnderMin = false;
            $isOverMax = false;
            if ($type === 'text') {
                $isNull = is_null($postData[$param]);
                if (isset($minCharCount)) $isUnderMin = mb_strlen($postData[$param]) < $minCharCount;
                if (isset($maxCharCount)) $isOverMax = mb_strlen($postData[$param]) > $maxCharCount;
            } else if ($type === 'file') {
                $isNull = is_null($fileData[$param]);
            }
            if ($isNull) {
                throw new InvalidRequestParameterException("'{$param}' must be set in request.");
            }
            if ($isUnderMin) {
                throw new InvalidRequestParameterException("'{$param}' must contain at least {$minCharCount} characters.");
            }
            if ($isOverMax) {
                throw new InvalidRequestParameterException("'{$param}' can contain up to {$maxCharCount} characters.");
            }
        }
        $profileImage = null;
        if (isset($fileData['profile_image']) && $fileData['profile_image']['tmp_name'] !== '') {
            ValidationHelper::validateUploadedImage('profile_image');
            $profileImage = $fileData['profile_image'];
        }
        $this->username = $postData['username'];
        $this->password = $postData['password'];
        $this->email = $postData['email'];
        $this->selfIntroduction = $postData['self_introduction'];
        $this->profileImage = $profileImage;
        $this->country = $postData['country'];
        $this->state = $postData['state'];
        $this->city = $postData['city'];
        $this->town = $postData['town'];
        $this->hobby_1 = $postData['hobby_1'];
        $this->hobby_2 = $postData['hobby_2'];
        $this->hobby_3 = $postData['hobby_3'];
        $this->career_1 = $postData['career_1'];
        $this->career_2 = $postData['career_2'];
        $this->career_3 = $postData['career_3'];
        ValidationHelper::validateUsername($this->username);
        ValidationHelper::validatePassword($this->password);
        ValidationHelper::validateEmailAddress($this->email);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSelfIntroduction(): string
    {
        return $this->selfIntroduction;
    }

    public function getProfileImage(): ?array
    {
        return $this->profileImage;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getTown(): string
    {
        return $this->town;
    }

    public function getHobbies(): array
    {
        $hobbies = [];
        if (isset($this->hobby_1) && $this->hobby_1 !== '') array_push($hobbies, $this->hobby_1);
        if (isset($this->hobby_2) && $this->hobby_2 !== '') array_push($hobbies, $this->hobby_2);
        if (isset($this->hobby_3) && $this->hobby_3 !== '') array_push($hobbies, $this->hobby_3);
        return $hobbies;
    }

    public function getCareers(): array
    {
        $careers = [];
        if (isset($this->career_1) && $this->career_1 !== '') array_push($careers, $this->career_1);
        if (isset($this->career_2) && $this->career_2 !== '') array_push($careers, $this->career_2);
        if (isset($this->career_3) && $this->career_3 !== '') array_push($careers, $this->career_3);
        return $careers;
    }
}
