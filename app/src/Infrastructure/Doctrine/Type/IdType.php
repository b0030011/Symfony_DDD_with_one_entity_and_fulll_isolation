<?php

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\User\ValueObject\Id;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class IdType extends Type
{
    public const string NAME = 'user_id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Id
    {
        return $value !== null ? new Id($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        return $value instanceof Id ? $value->value() : null;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
