<?php

namespace App\Infrastructure\Doctrine\Adapter;

use App\Domain\User\Entity\User as DomainUser;
use App\Domain\User\ValueObject\Password;
use App\Domain\User\ValueObject\Roles;
use App\Infrastructure\Doctrine\Entity\User\DoctrineUser;

class UserAdapter
{
    public static function toDomain(DoctrineUser $doctrineUser): DomainUser
    {
        return DomainUser::create(
            $doctrineUser->getId(),
            $doctrineUser->getEmail(),
            $doctrineUser->getAddress(),
            new Roles($doctrineUser->getRoles()),
            new Password($doctrineUser->getPassword())
        );
    }

    public static function populateDoctrine(DomainUser $domainUser, DoctrineUser $doctrineUser): void
    {
        $doctrineUser->setId($domainUser->getId()?->value());
        $doctrineUser->setEmail($domainUser->getEmail());
        $doctrineUser->setAddress($domainUser->getAddress());
        $doctrineUser->setRoles($domainUser->getRoles());
        $doctrineUser->setPassword($domainUser->getPassword());
    }

    public static function toDoctrine(DomainUser $domainUser): DoctrineUser
    {
        $doctrineUser = new DoctrineUser();
        self::populateDoctrine($domainUser, $doctrineUser);
        return $doctrineUser;
    }
}
