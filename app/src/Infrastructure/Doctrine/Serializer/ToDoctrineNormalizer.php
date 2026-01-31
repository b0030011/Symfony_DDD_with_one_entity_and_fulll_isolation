<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Serializer;

use App\Domain\Contract\ValueObjectInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ToDoctrineNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    public function setSerializer(SerializerInterface $serializer): void
    {
        // Not used here
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array|\ArrayObject|bool|float|int|null|string
    {
        if ($data instanceof ValueObjectInterface) {
            return $data->value();
        }
        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ValueObjectInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            ValueObjectInterface::class => true,
        ];
    }
}
