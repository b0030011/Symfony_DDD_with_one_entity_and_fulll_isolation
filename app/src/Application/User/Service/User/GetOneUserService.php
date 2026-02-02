<?php

declare(strict_types=1);

namespace App\Application\User\Service\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;

readonly class GetOneUserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(int $id): ?User
    {
        return $this->userRepository->getOneById($id);
    }
}
