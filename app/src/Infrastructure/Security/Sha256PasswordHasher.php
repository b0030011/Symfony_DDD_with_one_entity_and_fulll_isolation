<?php

namespace App\Infrastructure\Security;

use App\Domain\Contract\PasswordHasherInterface;

class Sha256PasswordHasher implements PasswordHasherInterface
{
    public function hash(string $password): string
    {
        return hash('sha256', $password);
    }
}
