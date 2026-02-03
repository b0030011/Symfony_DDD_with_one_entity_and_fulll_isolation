<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\User;

use App\Domain\Shared\ValueObject\PaginatedResult;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Doctrine\Adapter\UserAdapter;
use App\Infrastructure\Doctrine\Entity\User\DoctrineUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineUserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DoctrineUser::class);
    }

    public function delete(User $user): void
    {
        $id = $user->getId();
        if (!$id) {
            return;
        }

        $doctrineUser = $this->find($id->value());

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
        $doctrineUser = $this->find($id);
        return $doctrineUser ? UserAdapter::toDomain($doctrineUser) : null;
    }

    public function doctrineFindOneById(int $id): object|string|null
    {
        return $this->find($id);
    }

    public function existsByEmail(Email $email): bool
    {
        return $this->findOneBy(['email.email' => $email->value()]) !== null;
    }

    public function getAll(int $page = 1, int $limit = 10): PaginatedResult
    {
        $offset = max(0, ($page - 1) * $limit);

        $doctrineUsers = $this->createQueryBuilder('u')
            ->orderBy('u.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $countQb = $this->createQueryBuilder('u')->select('COUNT(u.id)');
        $total = (int)$countQb->getQuery()->getSingleScalarResult();

        $items = array_map(
            static fn(DoctrineUser $user) => UserAdapter::toDomain($user),
            $doctrineUsers
        );

        return new PaginatedResult($items, $total, $limit);
    }

    public function findOneByEmail(Email $email): ?User
    {
        $doctrineUser = $this->findOneBy(['email' => $email]);
        return $doctrineUser ? UserAdapter::toDomain($doctrineUser) : null;
    }
}
