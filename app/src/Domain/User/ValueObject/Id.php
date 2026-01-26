<?php

namespace App\Domain\User\ValueObject;

use App\Domain\Contract\ValueObjectInterface;

final readonly class Id implements ValueObjectInterface
{
    public function __construct(private ?int $id = null)
    {
        $this->validate();
    }

    private function validate(): void
    {
    }

    public function value(): int
    {
        return $this->id;
    }
}
