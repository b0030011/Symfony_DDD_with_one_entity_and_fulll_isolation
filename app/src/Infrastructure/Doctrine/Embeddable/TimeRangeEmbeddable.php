<?php

namespace App\Infrastructure\Doctrine\Embeddable;

use App\Domain\Shared\ValueObject\TimeRange;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class TimeRangeEmbeddable
{
    #[ORM\Column(name: "created_at", type: "datetime_immutable")]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: "updated_at", type: "datetime_immutable")]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ) {
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function fromDomain(TimeRange $timestamp): self
    {
        return new self(
            $timestamp->getCreatedAt(),
            $timestamp->getUpdatedAt()
        );
    }

    public function toDomain(): TimeRange
    {
        return new TimeRange(
            $this->createdAt,
            $this->updatedAt
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
}
