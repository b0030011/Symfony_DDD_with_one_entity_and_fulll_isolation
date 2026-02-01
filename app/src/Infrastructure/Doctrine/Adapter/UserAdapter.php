<?php

namespace App\Infrastructure\Doctrine\Adapter;

use App\Domain\User\Entity\User as DomainUser;
use App\Domain\User\ValueObject\Id;
use App\Domain\User\ValueObject\Password;
use App\Domain\User\ValueObject\Roles;
use App\Infrastructure\Doctrine\Entity\User\DoctrineUser;
use App\Infrastructure\Doctrine\Embeddable\AddressEmbeddable;
use App\Infrastructure\Doctrine\Embeddable\EmailEmbeddable;

final class UserAdapter
{
    public static function createFromDomain(DomainUser $user): DoctrineUser
    {
        $doctrineUser = new DoctrineUser();

        $doctrineUser->setAddress(AddressEmbeddable::fromDomain($user->getAddress()));
        $doctrineUser->setEmail(EmailEmbeddable::fromDomain($user->getEmail()));
        $doctrineUser->setRoles($user->getRoles()->value());
        $doctrineUser->setPassword($user->getPassword()->value());

        if ($user->getId()) {
            $doctrineUser->setId($user->getId()->value());
        }

        return $doctrineUser;
    }

    public static function updateDoctrineUser(DomainUser $user, DoctrineUser $doctrineUser): void
    {
        $doctrineUser->setEmail(
            new EmailEmbeddable($user->getEmail()->value())
        );

        $doctrineUser->setAddress(
            new AddressEmbeddable(
                $user->getAddress()->getIndex(),
                $user->getAddress()->getCity(),
                $user->getAddress()->getStreet()
            )
        );

        $doctrineUser->setRoles($user->getRoles()->value());
        $doctrineUser->setPassword($user->getPassword()->value());
    }

    public static function toDomain(DoctrineUser $entity): DomainUser
    {
        return DomainUser::create(
            $entity->getId() ? new Id($entity->getId()) : null,
            $entity->getEmail()->toDomain(),
            $entity->getAddress()->toDomain(),
            new Roles($entity->getRoles()),
            new Password($entity->getPassword())
        );
    }
}
