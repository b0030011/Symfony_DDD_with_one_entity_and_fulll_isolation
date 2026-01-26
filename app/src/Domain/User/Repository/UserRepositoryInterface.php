<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\Email;

interface UserRepositoryInterface
{
    /**
    * @return array<User>
    */
    public function getAll(): array;
    public function getOneById(int $id): ?User;
    public function store(User $user): void;
    public function update(User $user): void;
    public function delete(User $user): void;
    public function existsByEmail(Email $email): bool;
}
