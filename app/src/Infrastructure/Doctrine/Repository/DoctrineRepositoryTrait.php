<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\User\ValueObject\Id;
use Doctrine\ORM\Exception\ORMException;

trait DoctrineRepositoryTrait
{
    private function _getOneById(int $id): ?object
    {
        $doctrineObject = parent::getEntityManager()->find(static::DOCTRINE_CLASS_NAME, $id);
        return $this->getOneOrNothimg($doctrineObject);
    }

    private function getOneOrNothimg(?object $doctrineObject): mixed
    {
        return $doctrineObject ? $this->mapper->fromDoctrine($doctrineObject) : null;
    }

    private function _delete(object $domainObject): void
    {
        parent::getEntityManager()->remove(
            $this->mapper->toDoctrine($domainObject)
        );

        parent::getEntityManager()->flush();
    }

    private function _store(object $domainObject): void
    {
        $doctrineObject = $this->mapper->toDoctrine($domainObject);
        $this->getEntityManager()->persist($doctrineObject);
        $this->getEntityManager()->flush();
        $domainObject->setId(new ID($doctrineObject->getId()));
    }

    /**
     * @throws ORMException
     */
    private function _update(object $domainObject): void
    {
        $reference = $this->getEntityManager()
            ->getReference(static::DOCTRINE_CLASS_NAME, $domainObject->getId()->value());

        $this->mapper->toDoctrine($domainObject, $reference);
        $this->getEntityManager()->flush();
    }
}
