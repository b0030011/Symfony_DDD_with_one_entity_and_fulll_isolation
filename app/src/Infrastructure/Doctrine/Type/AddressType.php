<?php

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\User\ValueObject\Address;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class AddressType extends Type
{
    public const string NAME = 'user_address';

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
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Address
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            return null;
        }

        return new Address(
            $data['Index'] ?? '',
            $data['city'] ?? '',
            $data['Street'] ?? ''
        );
    }

    /**
     * @throws \JsonException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Address) {
            return json_encode($value->value(), JSON_THROW_ON_ERROR);
        }

        return null;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
