<?php

declare(strict_types=1);

namespace App\Infrastructure\Presentation\Api\User\ViewModel;

use App\Domain\Shared\Pagination\PaginatedResult;
use App\Domain\User\Entity\User;

class UserListViewModel
{
    private array $items;
    private array $meta;

    public function __construct(PaginatedResult $paginatedResult, int $currentPage, int $perPage)
    {
        $this->items = array_map(
            static fn(User $user) => new UserViewModel($user),
            $paginatedResult->items()
        );

        $this->meta = [
            'total' => $paginatedResult->total(),
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'total_pages' => $paginatedResult->totalPages(),
            'has_more' => $currentPage < $paginatedResult->totalPages()
        ];
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(
                static fn(UserViewModel $vm) => $vm->toArray(),
                $this->items
            ),
            'meta' => $this->meta
        ];
    }
}
