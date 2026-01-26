<?php

namespace App\Application\User\Service\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;

readonly class GetAllUsersService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(): array
    {
        $users = $this->userRepository->getAll();

        return array_map(static function (User $user) {
            return [
                'id' => $user->getId()->value(),
                'email' => $user->getEmail()->value(),
                'roles' => $user->getRoles()->value()
            ];
        }, $users);
    }
}
