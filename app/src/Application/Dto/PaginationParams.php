<?php

declare(strict_types=1);

namespace App\Application\Dto;

readonly class PaginationParams
{
    public function __construct(
        public int $page,
        public int $limit
    ) {}
}
