<?php

namespace App\Domain\User\Repository;

use App\Domain\Pagination\PaginatedResult;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\Email;

interface UserRepositoryInterface
{
    public function getAll(int $page, int $limit): PaginatedResult;
    public function getOneById(int $id): ?User;
    public function save(User $user): void;
    public function delete(User $user): void;
    public function existsByEmail(Email $email): bool;
}
