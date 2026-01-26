<?php

namespace App\Domain\Contract;

interface ValueObjectInterface
{
    /** @return scalar|array|null */
    public function value(): float|int|bool|array|string|null;
}
