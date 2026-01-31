<?php

namespace App\Domain\User\ValueObject;

use App\Domain\Contract\ValueObjectInterface;

final readonly class Address implements ValueObjectInterface
{
    public function __construct(private string $index, private string $city, private string $street)
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
            'city' => $this->city,
            'Street' => $this->street
        ];
    }
}
