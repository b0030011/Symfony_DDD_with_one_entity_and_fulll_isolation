<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

final readonly class PaginatedResult
{
    public function __construct(private array $items, private int $total, private int $perPage)
    {
        if ($total < 0) {
            throw new \InvalidArgumentException('Total cannot be negative');
        }

        if ($perPage < 1) {
            throw new \InvalidArgumentException('Per page must be at least 1');
        }
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
