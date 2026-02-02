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

    public static function fromArray(array $data): self
    {
        return new self(
            $data['index'] ?? '',
            $data['city'] ?? '',
            $data['street'] ?? ''
        );
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getStreet(): string
    {
        return $this->street;
    }
}
