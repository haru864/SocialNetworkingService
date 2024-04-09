<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class PostProfileRequest
{
    private string $action;
    private string $username;
    private string $password;
    private string $email;
    private string $selfIntroduction;
    private ?array $profileImage;  // $_FILESオブジェクト
    private string $country;
    private string $state;
    private string $city;
    private string $town;
    private array $hobbies;
    private array $careers;

    public function __construct($postData, $fileData)
    {
        if ($postData['action'] !== 'edit' && $postData['action'] !== 'delete') {
            throw new InvalidRequestParameterException("'action' must be 'edit' or 'delete'.");
        }
        $this->action = $postData['action'];
        if ($this->action === 'delete') {
            return;
        }
        $requiredParams = [
            'action' => 'text',
            'username' => 'text',
            'password' => 'text',
            'email' => 'text',
            'self_introduction' => 'text',
            // 'profile_image' => 'file',
            'country' => 'text',
            'state' => 'text',
            'city' => 'text',
            'town' => 'text',
            'hobbies' => 'text',
            'careers' => 'text'
        ];
        foreach ($requiredParams as $param => $dataType) {
            $isNullParam = true;
            if ($dataType === 'text') {
                $isNullParam = is_null($postData[$param]);
            } else if ($dataType === 'file') {
                $isNullParam = is_null($fileData[$param]);
            }
            if ($isNullParam) {
                throw new InvalidRequestParameterException("'{$param}' must be set to edit profile.");
            }
        }
        $maxHobbiesCount = 3;
        if (count($postData['hobbies']) > $maxHobbiesCount) {
            throw new InvalidRequestParameterException("'hobbies' must be no more than {$maxHobbiesCount}.");
        }
        $maxCareersCount = 3;
        if (count($postData['careers']) > $maxCareersCount) {
            throw new InvalidRequestParameterException("'careers' must be no more than {$maxCareersCount}.");
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
        $this->hobbies = $postData['hobbies'];
        $this->careers = $postData['careers'];
    }

    public function getAction(): string
    {
        return $this->action;
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
        return $this->hobbies;
    }

    public function getCareers(): array
    {
        return $this->careers;
    }
}
