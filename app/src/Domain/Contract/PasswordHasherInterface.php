<?php

namespace App\Domain\Contract;

interface PasswordHasherInterface
{
    public function hash(string $password): string;
}
