<?php

namespace App\Domain\Shared\ValueObject;

final class TimeRange
{
    public function __construct(
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
    }

    public function update(): self
    {
        return new self(
            $this->createdAt,
            new \DateTimeImmutable()
        );
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self
            && $this->createdAt == $other->createdAt
            && $this->updatedAt == $other->updatedAt;
    }
}
