<?php

namespace App\Infrastructure\Doctrine\Type;
namespace App\Infrastructure\Doctrine\Type;

use App\Domain\User\ValueObject\Roles;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class RolesType extends Type
{
    public const string NAME = 'user_roles';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    /**
     * @throws \JsonException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Roles
    {
        if ($value === null) {
            return null;
        }

        $roles = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($roles)) {
            return null;
        }

        return new Roles($roles);
    }

    /**
     * @throws \JsonException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Roles) {
            return json_encode($value->value(), JSON_THROW_ON_ERROR);
        }

        return null;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
