<?php

namespace App\Infrastructure\Doctrine\Repository\User;

use App\Domain\Shared\Pagination\PaginatedResult;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Doctrine\Entity\User\DoctrineUser;
use App\Infrastructure\Doctrine\Mapper\DoctrineUserMapper;
use App\Infrastructure\Doctrine\Repository\DoctrineRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineUserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    use DoctrineRepositoryTrait;

    const string DOCTRINE_CLASS_NAME = DoctrineUser::class;

    public function __construct(
       protected DoctrineUserMapper $mapper,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, self::DOCTRINE_CLASS_NAME);
    }
    public function delete(User $user): void
    {
        $this->_delete($user);
    }

    public function store(User $user): void
    {
        $this->_store($user);
    }

    public function update(User $user): void
    {
        $this->_update($user);
    }

    public function getOneById(int $id): ?User
    {
        return $this->_getOneById($id);
    }

    /**
     * @throws ORMException
     */
    public function doctrineFindOneById(int $id): object|string|null
    {
        return $this->getEntityManager()->find(self::DOCTRINE_CLASS_NAME, $id);
    }

    public function existsByEmail(Email $email): bool
    {
        $user = $this->getEntityManager()->getRepository(self::DOCTRINE_CLASS_NAME)
            ->findOneBy(['email' => $email->value()]);

        return $user !== null;
    }

    public function getAll(int $page = 1, int $limit = 10): PaginatedResult
    {
        $offset = max(0, ($page - 1) * $limit);

        $doctrineUsers = $this->getEntityManager()
            ->getRepository(self::DOCTRINE_CLASS_NAME)
            ->createQueryBuilder('u')
            ->orderBy('u.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $countQb = $this->createQueryBuilder('u')->select('COUNT(u.id)');
        $total = (int)$countQb->getQuery()->getSingleScalarResult();

        $items = array_map(
            fn(DoctrineUser $user) => $this->mapper->fromDoctrine($user),
            $doctrineUsers
        );

        return new PaginatedResult($items, $total, $limit);
    }
}
