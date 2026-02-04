<?php

namespace App\Domain\User\Entity;

use App\Domain\Shared\ValueObject\TimeRange;
use App\Domain\User\ValueObject\Address;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\Id;
use App\Domain\User\ValueObject\Password;
use App\Domain\User\ValueObject\Roles;

class User
{
    private function __construct(
        private ?Id $id,
        private ?Email $email,
        private ?Address $address,
        private ?Roles $roles,
        private ?Password $password,
        private ?TimeRange $timestamp
    ) {
        $this->validate();

        if ($timestamp === null) {
            $this->timestamp = new TimeRange();
        }
    }

    public static function create(
        ?Id $id,
        Email $email,
        Address $address,
        Roles $roles,
        Password $password,
        ?TimeRange $timestamp = null
    ): self {
        return new self($id, $email, $address, $roles, $password, $timestamp);
    }

    private function validate(): void
    {
        // Бизнес-правило: роль админа требует подтвержденного email
    }

    public function getId(): ?Id
    {
        return $this->id;
    }

    private function setId(Id $id): static
    {
        if (empty($this->id)) {
            $this->id = $id;
        }

        return $this;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    private function setEmail(Email $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): Roles
    {
        return $this->roles;
    }

    private function setRoles(Roles $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    private function setPassword(Password $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    private function setAddress(Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getTimestamp(): TimeRange
    {
        return $this->timestamp;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->timestamp->getCreatedAt();
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->timestamp->getUpdatedAt();
    }
}
