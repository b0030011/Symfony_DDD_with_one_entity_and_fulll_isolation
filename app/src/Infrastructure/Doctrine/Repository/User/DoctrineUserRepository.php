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
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, DoctrineUser::class);
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
        $doctrineUser = $this->find($user->getId()->value());
        UserAdapter::populateDoctrine($user, $doctrineUser);
        $this->getEntityManager()->flush();
    }

    public function existsByEmail(Email $email): bool
    {
        return $this->findOneBy(['email' => $email->value()]) !== null;
    }

    public function getOneById(int $id): ?User
    {
        $doctrineObject = $this->find(new Id($id));
        return $doctrineObject ? UserAdapter::toDomain($doctrineObject) : null;
    }

    public function getAll(): array
    {
        $doctrineUsers = $this->findAll();

        return array_map(
            static fn(DoctrineUser $doctrineUser) => UserAdapter::toDomain($doctrineUser),
            $doctrineUsers
        );
    }
}
