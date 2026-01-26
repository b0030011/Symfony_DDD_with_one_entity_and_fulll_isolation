<?php

namespace App\Infrastructure\Doctrine\Repository\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Doctrine\Entity\User\DoctrineUser;
use App\Infrastructure\Doctrine\Mapper\DoctrineUserMapper;
use App\Infrastructure\Doctrine\Repository\DoctrineRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
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
     * @throws OptimisticLockException
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

    public function getAll(): array
    {
        $doctrineUsers = $this->getEntityManager()
            ->getRepository(self::DOCTRINE_CLASS_NAME)
            ->findAll();

        return array_map(
            fn(DoctrineUser $user) => $this->mapper->fromDoctrine($user),
            $doctrineUsers
        );
    }
}
