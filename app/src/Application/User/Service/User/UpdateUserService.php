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

readonly class UpdateUserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher
    ) {}

    public function __invoke(array $data, int $id): void
    {
        $user = $this->userRepository->getOneById($id);

        if (null === $user) {
            throw new \DomainException('User not found');
        }

        $email = $user->getEmail();
        $address = $user->getAddress();
        $roles = $user->getRoles();
        $password = $user->getPassword();

        if (isset($data['email'])) {
            $newEmail = new Email($data['email']);
            if ($newEmail->value() !== $user->getEmail()->value()) {
                if ($this->userRepository->existsByEmail($newEmail)) {
                    throw new \DomainException('Email already exists');
                }
                $email = $newEmail;
            }
        }

        if (isset($data['password'])) {
            $hashedPassword = $this->passwordHasher->hash($data['password']);
            $password = new Password($hashedPassword);
        }

        if (isset($data['index'], $data['street'], $data['city'])) {
            $address = new Address($data['index'], $data['city'], $data['street']);
        }

        if (isset($data['role'])) {
            $roles = new Roles($data['role']);
        }

        $updatedUser = User::create(
            $user->getId(),
            $email,
            $address,
            $roles,
            $password
        );

        $this->userRepository->save($updatedUser);
    }
}
