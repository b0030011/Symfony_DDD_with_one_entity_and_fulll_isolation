<?php

namespace App\Application\User\Service\User;

use App\Domain\User\Repository\UserRepositoryInterface;

readonly class GetOneUserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(int $id): ?array
    {
        $user = $this->userRepository->getOneById($id);

        if (null === $user) {
            return null;
        }

        return [
            'id' => $user->getId()->value(),
            'email' => $user->getEmail()->value(),
            'roles' => $user->getRoles()->value()
        ];
    }
}
