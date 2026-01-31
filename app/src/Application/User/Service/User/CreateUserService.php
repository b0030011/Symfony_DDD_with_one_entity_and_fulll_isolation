<?php

declare(strict_types=1);

namespace App\Application\User\Service\User;

use App\Domain\Contract\PasswordHasherInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Address;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\Password;
use App\Domain\User\ValueObject\Roles;

readonly class CreateUserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher
    ) {}

    public function __invoke(array $data): void
    {
        $email = new Email($data['email']);
        if ($this->userRepository->existsByEmail($email)) {
            throw new \DomainException('Email already exists');
        }

        new Password($data['password']);
        $hashedPassword = $this->passwordHasher->hash($data['password']);

        $user = User::create(
            null,
            $email,
            new Address($data['index'], $data['city'], $data['street']),
            new Roles($data['role']),
            new Password($hashedPassword)
        );

        $this->userRepository->store($user);
    }
}
