<?php

namespace App\Infrastructure\Doctrine\Mapper;

use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Entity\User\DoctrineUser;

readonly final class DoctrineUserMapper extends AbstractDoctrineMapper
{
    public const DOCTRINE_CLASS_NAME = DoctrineUser::class;
    public const DOMAIN_CLASS_NAME = User::class;
}
