<?php

declare(strict_types=1);

namespace App\Domain\Pagination;

final readonly class PaginatedResult
{
    public function __construct(private array $items, private int $total, private int $perPage)
    {
    }

    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function totalPages(): int
    {
        return (int)ceil($this->total / $this->perPage);
    }

    public function currentPage(int $page): int
    {
        return min($page, $this->totalPages());
    }
}
