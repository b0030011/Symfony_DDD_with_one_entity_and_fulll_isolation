<?php

namespace App\Infrastructure\DataFixtures\Test;

use App\Infrastructure\Doctrine\Entity\User\DoctrineUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public const string USER_1 = 'user-1';

    public function load(ObjectManager $manager): void
    {
        $user = new DoctrineUser()
            ->setEmail('john.doe@example.com')
            ->setPassword(password_hash('secret', PASSWORD_BCRYPT))
            ->setRoles(['ROLE_USER']);

        $manager->persist($user);

        $this->addReference(self::USER_1, $user);

        $manager->flush();
    }
}
