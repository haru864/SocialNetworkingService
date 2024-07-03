<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class Address implements Model
{
    use GenericModel;

    private ?int $id;
    private int $userId;
    private ?string $country;
    private ?string $state;
    private ?string $city;
    private ?string $town;

    public function __construct(
        ?int $id,
        int $userId,
        ?string $country,
        ?string $state,
        ?string $city,
        ?string $town
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->country = $country;
        $this->state = $state;
        $this->city = $city;
        $this->town = $town;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getTown(): string
    {
        return $this->town;
    }

    public function setTown(string $town): void
    {
        $this->town = $town;
    }

    public function toArray(): array
    {
        $data = [
            'country' => $this->getCountry(),
            'state' => $this->getState(),
            'city' => $this->getCity(),
            'town' => $this->getTown()
        ];
        return $data;
    }
}
