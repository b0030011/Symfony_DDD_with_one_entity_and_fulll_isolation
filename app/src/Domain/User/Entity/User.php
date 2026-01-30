<?php

namespace App\Domain\User\Entity;

use App\Domain\User\ValueObject\Address;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\Id;
use App\Domain\User\ValueObject\Password;
use App\Domain\User\ValueObject\Roles;

class User
{
    protected ?Id $id;
    private function __construct(
        private Email $email,
        private Address $address,
        private Roles $roles,
        private Password $password,
    ) {
        $this->id = null;
        $this->validate();
    }

    public static function create(
        Email $email,
        Address $address,
        Roles $roles,
        Password $password
    ): self {
        return new self($email, $address, $roles, $password);
    }

    private function validate(): void
    {
        // Бизнес-правило: роль админа требует подтвержденного email
    }

    public function getId(): ?ID
    {
        return $this->id;
    }

    public function setId(ID $id): static
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

    public function setEmail(Email $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): Roles
    {
        return $this->roles;
    }

    public function setRoles(Roles $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function setPassword(Password $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): static
    {
        $this->address = $address;

        return $this;
    }

}
