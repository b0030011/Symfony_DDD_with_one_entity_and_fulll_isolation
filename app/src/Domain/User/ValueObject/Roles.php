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

        foreach ($this->roles as $role) {
            if (!is_string($role) || !str_starts_with($role, 'ROLE_')) {
                throw new \DomainException('Role must start with ROLE_ prefix');
            }
        }
    }

    public function value(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }
}
