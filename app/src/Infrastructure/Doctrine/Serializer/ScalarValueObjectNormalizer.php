<?php

declare(strict_types=1);

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
        // Not used here
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array|\ArrayObject|bool|float|int|null|string
    {
        if ($data instanceof ValueObjectInterface) {
            return $data->value();
        }
        return $data;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): string|object
    {
        try {
            $reflection = new \ReflectionClass($type);
            $entity = $reflection->newInstanceWithoutConstructor();

            $VO = $this->extractVOData($reflection);

            foreach ($data as $propertyName => $value) {
                $this->setProperty($entity, $propertyName, $value, $reflection, $VO);
            }

            return $entity;

        } catch (\Throwable $e) {
            throw new NotNormalizableValueException(
                sprintf('Cannot denormalize %s from string "%s".', $type, $data),
                previous: $e
            );
        }
    }

    private function extractVOData(\ReflectionClass $reflection): array
    {
        $constructor = $reflection->getConstructor();
        $parameters = $constructor?->getParameters();

        $VO = [];

        foreach ($parameters ?? [] as $parameter) {
            if (!$parameter->hasType()) {
                throw new NotNormalizableValueException("Parameter {$parameter->getName()} must have a type.");
            }

            $name = lcfirst($parameter->getName());
            $type = $parameter->getType()->getName();
            $VO[$name] = $type;
        }

        return $VO;
    }

    /**
     * @throws \ReflectionException
     */
    private function setProperty(object $entity, string $propertyName, mixed $value, \ReflectionClass $reflection, array $vo): void
    {
        $setterMethodName = 'set' . ucfirst($propertyName);

        if ($reflection->hasMethod($setterMethodName)) {
            $setter = $reflection->getMethod($setterMethodName);
            $voType = $vo[$propertyName] ?? null;

            if (!$voType || !class_exists($voType)) {
                throw new NotNormalizableValueException(sprintf('VO class not found for property %s', $propertyName));
            }

            $voReflection = new \ReflectionClass($vo[$propertyName]);
            $constructor = $voReflection->getConstructor();

            if (!$constructor) {
                throw new NotNormalizableValueException(sprintf('Expected VO %s', $propertyName));
            }

            $numParameters = $constructor->getNumberOfParameters();

            if ($numParameters === 1) {
                $instance = new $voType($value);
                $setter->invoke($entity, $instance);
                return;
            }

            if (!is_array($value)) {
                throw new NotNormalizableValueException(
                    sprintf('Expected array for %s, got %s', $vo[ucfirst($propertyName)], gettype($value))
                );
            }

            $args = array_values($value);
            $instance = new $voType(...$args);
            $setter->invoke($entity, $instance);
            return;
        }

        if ($reflection->hasProperty($propertyName)) {
            $property = $reflection->getProperty($propertyName);
            $property->setValue($entity, $value);
        }
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ValueObjectInterface;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return class_exists($type) &&
            !is_subclass_of($type, ValueObjectInterface::class) &&
            strpos($type, 'App\Domain\\') === 0;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => false,
        ];
    }
}
