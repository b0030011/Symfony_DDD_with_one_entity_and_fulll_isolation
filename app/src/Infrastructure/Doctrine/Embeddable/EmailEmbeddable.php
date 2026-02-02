<?php

namespace App\Infrastructure\Doctrine\Embeddable;

use App\Domain\User\ValueObject\Email;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class EmailEmbeddable
{
    #[ORM\Column(name: "email", type: "string", length: 180, unique: true)]
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function fromDomain(Email $email): self
    {
        return new self($email->value());
    }

    public function toDomain(): Email
    {
        return new Email($this->email);
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
