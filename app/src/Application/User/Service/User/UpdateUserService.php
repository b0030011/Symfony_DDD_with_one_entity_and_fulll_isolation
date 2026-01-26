<?php

namespace App\Application\User\Service\User;

use App\Domain\Contract\PasswordHasherInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
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
        /** @var User $user */
        $user = $this->userRepository->getOneById($id);

        if (null === $user) {
            throw new \DomainException('User not found');
        }

        if (isset($data['email'])) {
            $user->setEmail(new Email($data['email']));
        }

        if (isset($data['role'])) {
            $user->setRoles(new Roles($data['role']));
        }

        if (isset($data['password'])) {
            new Password($data['password']);
            $hashedPassword = $this->passwordHasher->hash($data['password']);
            $user->setPassword(new Password($hashedPassword));
        }

        $this->userRepository->update($user);
    }
}
