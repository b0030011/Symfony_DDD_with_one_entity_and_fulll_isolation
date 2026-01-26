<?php

namespace App\Domain\User\ValueObject;

use App\Domain\Contract\ValueObjectInterface;

readonly class Roles implements ValueObjectInterface
{
    public function __construct(private array $roles)
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (count($this->roles) <= 0) {
            throw new \DomainException('Password must be at least 8 characters');
        }

//        if (!in_array($this->roles, ['ROLE_ADMIN', 'ROLE_MODERATOR'])) {
//            throw new \DomainException('Password must contain uppercase letter');
//        }
    }

    public function value(): array
    {
        return $this->roles;
    }
}
