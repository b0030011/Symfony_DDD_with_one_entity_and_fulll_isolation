<?php

namespace App\Infrastructure\Doctrine\Serializer;

use App\Domain\Contract\ValueObjectInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ScalarValueObjectNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    public function setSerializer(SerializerInterface $serializer): void
    {
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array|\ArrayObject|bool|float|int|null|string
    {
        if ($data instanceof ValueObjectInterface) {
            return $data->value();
        }
        return $data;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        try {
            $reflection = new \ReflectionClass($type);
            $constructor = $reflection->getConstructor();

            if (!$constructor) {
                throw new NotNormalizableValueException(
                    sprintf('Expected VO %s', $type)
                );
            }

            $numParameters = $constructor->getNumberOfParameters();
            if ($numParameters === 1) {
                return new $type($data);
            }

            if (!is_array($data)) {
                throw new NotNormalizableValueException(
                    sprintf('Expected array for %s, got %s', $type, gettype($data))
                );
            }

            $args = [];
            foreach ($data as $param) {
                $args[] = $param;
            }

            return new $type(...$args);

        } catch (\Throwable $e) {
            throw new NotNormalizableValueException(
                sprintf('Cannot denormalize %s from string "%s".', $type, $data),
                previous: $e
            );
        }
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ValueObjectInterface;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_subclass_of($type, ValueObjectInterface::class) === true;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            ValueObjectInterface::class => true,
        ];
    }
}
