<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Doctrine\Adapter\UserAdapter;
use App\Infrastructure\Doctrine\Entity\User\DoctrineUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineUserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    const string DOCTRINE_CLASS_NAME = DoctrineUser::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::DOCTRINE_CLASS_NAME);
    }

    public function delete(User $user): void
    {
        $id = $user->getId();
        if (!$id) {
            return;
        }

        $doctrineUser = $this->getEntityManager()->find(
            self::DOCTRINE_CLASS_NAME,
            $id->value()
        );

        if ($doctrineUser) {
            $this->getEntityManager()->remove($doctrineUser);
            $this->getEntityManager()->flush();
        }
    }

    public function save(User $user): void
    {
        $id = $user->getId()?->value();
        $doctrineUser = $id ? $this->find($id) : null;

        if (!$doctrineUser) {
            $doctrineUser = UserAdapter::createFromDomain($user);
        } else {
            UserAdapter::updateDoctrineUser($user, $doctrineUser);
        }

        $this->getEntityManager()->persist($doctrineUser);
        $this->getEntityManager()->flush();
    }

    public function getOneById(int $id): ?User
    {
        $doctrineUser = $this->getEntityManager()->find(DoctrineUser::class, $id);
        return $doctrineUser ? UserAdapter::toDomain($doctrineUser) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function doctrineFindOneById(int $id): object|string|null
    {
        return $this->getEntityManager()->find(self::DOCTRINE_CLASS_NAME, $id);
    }

    public function existsByEmail(Email $email): bool
    {
        $doctrineUser = $this->getEntityManager()
            ->getRepository(self::DOCTRINE_CLASS_NAME)
            ->findOneBy(['email.email' => $email->value()]);

        return $doctrineUser !== null;
    }

    public function getAll(): array
    {
        $doctrineUsers = $this->getEntityManager()
            ->getRepository(self::DOCTRINE_CLASS_NAME)
            ->findAll();

        if (empty($doctrineUsers)) {
            return [];
        }

        return array_map(
            static fn(DoctrineUser $doctrineUser) => UserAdapter::toDomain($doctrineUser),
            $doctrineUsers
        );
    }

    public function findOneByEmail(Email $email): ?User
    {
        $doctrineUser = $this->getEntityManager()->getRepository(self::DOCTRINE_CLASS_NAME)
            ->findOneBy(['email' => $email]);

        if (!$doctrineUser) {
            return null;
        }

        return UserAdapter::toDomain($doctrineUser);
    }
}
