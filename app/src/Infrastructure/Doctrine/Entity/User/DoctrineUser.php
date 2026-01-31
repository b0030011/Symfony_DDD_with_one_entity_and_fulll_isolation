<?php

namespace App\Infrastructure\Doctrine\Entity\User;

use App\Domain\User\ValueObject\Address;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\Id;
use App\Domain\User\ValueObject\Password;
use App\Domain\User\ValueObject\Roles;
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
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'user_id')]
    private ?Id $id;

    #[ORM\Column(type: 'user_address')]
    private Address $address;

    #[ORM\Column(type: 'user_email', length: 180, unique: true)]
    private Email|null $email;

    #[ORM\Column(type: 'user_roles')]
    private Roles $roles;

    #[ORM\Column(type: 'user_password')]
    private ?Password $password;

    public function getUserIdentifier(): string
    {
        return $this->email ? $this->email->value() : '';
    }

    public function getId(): ?Id
    {
        return $this->id ?? null;
    }

    public function setId(?int $id): void
    {
        $this->id = new Id($id);
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function setEmail(?Email $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles->value() ?: ['ROLE_USER'];
    }

    public function setRoles(Roles $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password->value();
    }

    public function setPassword(?Password $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }
}
