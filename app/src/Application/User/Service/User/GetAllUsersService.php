<?php

declare(strict_types=1);

namespace App\Application\User\Service\User;

use App\Application\Dto\PaginationParams;
use App\Domain\Shared\Pagination\PaginatedResult;
use App\Domain\User\Repository\UserRepositoryInterface;

readonly class GetAllUsersService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(PaginationParams $params): PaginatedResult
    {
        return $this->userRepository->getAll(
            page: $params->page,
            limit: $params->limit
        );
    }
}
