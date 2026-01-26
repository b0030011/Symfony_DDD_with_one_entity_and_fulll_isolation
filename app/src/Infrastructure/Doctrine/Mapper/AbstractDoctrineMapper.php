<?php

namespace App\Infrastructure\Doctrine\Mapper;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

readonly abstract class AbstractDoctrineMapper
{
    public const DOCTRINE_CLASS_NAME = null;
    public const DOMAIN_CLASS_NAME = null;

    public function __construct(
        protected SerializerInterface $serializer,
    )
    {
    }

    /**
     * Преобразование доменного объекта в сущность Doctrine
     * @param object $domainObject
     * @param object|null $entity
     * @return object
     */
    public function toDoctrine(object $domainObject, ?object $entity = null): object
    {
        $normalizedData = \array_filter($this->serializer->normalize($domainObject));
        $options = $entity ? [AbstractNormalizer::OBJECT_TO_POPULATE => $entity] : [];

        return $this->serializer->denormalize(
            $normalizedData,
            static::DOCTRINE_CLASS_NAME,
            null,
            $options
        );
    }

    /**
     * Преобразование сущности Doctrine в доменный объект
     * @param object $doctrineObject Сущность Doctrine
     * @return ?object Доменный объект
     */
    public function fromDoctrine(object $doctrineObject): ?object
    {
        $normalizedData = array_filter($this->serializer->normalize($doctrineObject));
        return $this->serializer->denormalize(
            $normalizedData,
            static::DOMAIN_CLASS_NAME
        );
    }
}
