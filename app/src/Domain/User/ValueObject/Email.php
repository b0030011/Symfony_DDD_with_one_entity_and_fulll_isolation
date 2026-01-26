<?php

namespace App\Domain\User\ValueObject;

use App\Domain\Contract\ValueObjectInterface;

final class Email implements ValueObjectInterface
{
    public function __construct(private string $email)
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
    }

    public function value(): string
    {
        return $this->email;
    }
}
