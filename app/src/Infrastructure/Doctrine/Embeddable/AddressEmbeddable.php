<?php

namespace App\Infrastructure\Doctrine\Embeddable;

use App\Domain\User\ValueObject\Address;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class AddressEmbeddable
{
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $index;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $city;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $street;

    public function __construct(?string $index, ?string $city, ?string $street)
    {
        $this->index = $index;
        $this->city = $city;
        $this->street = $street;
    }

    public static function fromDomain(Address $address): self
    {
        return new self(
            $address->getIndex(),
            $address->getCity(),
            $address->getStreet()
        );
    }

    public function toDomain(): Address
    {
        return new Address(
            $this->index ?? '',
            $this->city ?? '',
            $this->street ?? ''
        );
    }

    public function getIndex(): ?string
    {
        return $this->index;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }
}

