<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\User;

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

    public function getAll(): array
    {
        $doctrineUsers = $this->findAll();

        return array_map(
            static fn(DoctrineUser $doctrineUser) => UserAdapter::toDomain($doctrineUser),
            $doctrineUsers
        );
    }

    public function findOneByEmail(Email $email): ?User
    {
        $doctrineUser = $this->findOneBy(['email' => $email]);
        return $doctrineUser ? UserAdapter::toDomain($doctrineUser) : null;
    }
}
