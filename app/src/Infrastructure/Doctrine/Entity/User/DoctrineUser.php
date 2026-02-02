<?php

namespace App\Infrastructure\Doctrine\Entity\User;

use App\Infrastructure\Doctrine\Embeddable\AddressEmbeddable;
use App\Infrastructure\Doctrine\Embeddable\EmailEmbeddable;
use App\Infrastructure\Doctrine\Repository\User\DoctrineUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: DoctrineUserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class DoctrineUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Embedded(class: AddressEmbeddable::class, columnPrefix: 'address_')]
    private AddressEmbeddable $address;

    #[ORM\Embedded(class: EmailEmbeddable::class, columnPrefix: false)]
    private EmailEmbeddable $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): EmailEmbeddable
    {
        return $this->email;
    }
    public function getAddress(): AddressEmbeddable
    {
        return $this->address;
    }

    public function getUserIdentifier(): string
    {
        return $this->email->getEmail();
    }

    public function getRoles(): array {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setEmail(EmailEmbeddable $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function setAddress(AddressEmbeddable $address): static
    {
        $this->address = $address; return $this;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;
        return $this;
    }
}
