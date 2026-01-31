<?php

namespace App\Infrastructure\Doctrine\Repository\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\Id;
use App\Infrastructure\Doctrine\Adapter\UserAdapter;
use App\Infrastructure\Doctrine\Entity\User\DoctrineUser;
use App\Infrastructure\Doctrine\Mapper\DoctrineUserMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineUserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    const string DOCTRINE_CLASS_NAME = DoctrineUser::class;

    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, self::DOCTRINE_CLASS_NAME);
    }
    public function delete(User $user): void
    {

    }

    public function save(User $user): void
    {
        if ($user->getId() === null) {
            $doctrineUser = UserAdapter::toDoctrine($user);
            $this->getEntityManager()->persist($doctrineUser);
            $this->getEntityManager()->flush();
        } else {
            $this->update($user);
        }
    }

    public function update(User $user): void
    {
        $doctrineUser = $this->getEntityManager()->getReference(
            self::DOCTRINE_CLASS_NAME,
            $user->getId()->value()
        );

        UserAdapter::populateDoctrine($user, $doctrineUser);
        $this->getEntityManager()->flush();
    }

    public function existsByEmail(Email $email): bool
    {
        $user = $this->getEntityManager()->getRepository(self::DOCTRINE_CLASS_NAME)
            ->findOneBy(['email' => $email->value()]);

        return $user !== null;
    }

    public function getOneById(int $id): ?User
    {
        $doctrineObject = $this->getEntityManager()->find(static::DOCTRINE_CLASS_NAME, new Id($id));
        return $doctrineObject ? UserAdapter::toDomain($doctrineObject) : null;
    }

    public function getAll(): array
    {
        $doctrineUsers = $this->getEntityManager()
            ->getRepository(self::DOCTRINE_CLASS_NAME)
            ->findAll();

        return array_map(
            static fn(DoctrineUser $doctrineUser) => UserAdapter::toDomain($doctrineUser),
            $doctrineUsers
        );
    }
}
