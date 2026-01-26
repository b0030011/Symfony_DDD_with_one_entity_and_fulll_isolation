<?php

namespace App\Domain\User\ValueObject;

use App\Domain\Contract\ValueObjectInterface;

final class Password implements ValueObjectInterface
{
    public function __construct(private string $password)
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (strlen($this->password) < 8) {
            throw new \DomainException('Password must be at least 8 characters');
        }

//        if (!preg_match('/[A-Z]/', $this->password)) {
//            throw new \DomainException('Password must contain uppercase letter');
//        }
    }

    public function value(): string
    {
        return $this->password;
    }
}
