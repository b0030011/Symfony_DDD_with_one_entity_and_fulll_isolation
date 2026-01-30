<?php

namespace App\Domain\User\ValueObject;

use App\Domain\Contract\ValueObjectInterface;

final readonly class Address implements ValueObjectInterface
{
    public function __construct(private string $index, private string $street)
    {
        $this->validate();
    }

    private function validate(): void
    {

    }

    public function value(): array
    {
        return [
            'Index' => $this->index,
            'Street' => $this->street
        ];
    }
}
