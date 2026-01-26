<?php declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class CriticalSerializer implements CriticalSerializerInterface
{
    private SerializerInterface $serializer;

    public function __construct()
    {
        $this->serializer = $this->createSerializer();
    }

    public function serialize(mixed $data, string $format = self::FORMAT, array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $this->buildContext($context));
    }

    public function deserialize(mixed $data, string $type, string $format = self::FORMAT, array $context = []): mixed
    {
        return $this->serializer->deserialize($data, $type, $format, $this->buildContext($context));
    }

    /** @param array<string, mixed> $context */
    public function deserializeInto(mixed $data, object $target, string $format = self::FORMAT, array $context = []): object
    {
        $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $target;

        return $this->deserialize($data, get_class($target), $format, $context);
    }

    private function buildContext(array $context): array
    {
        $defaultContext = [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => fn (object $object): ?int => method_exists($object, 'getId') ? $object->getId() : null,
            // Ignore Doctrine proxy internal properties (both camelCase and snake_case forms)
            AbstractNormalizer::IGNORED_ATTRIBUTES => [
                '__initializer__', '__cloner__', '__isInitialized__', '__is_initialized__',
                'lazyObjectState', 'lazy_object_state',
            ],
        ];

        return array_merge($defaultContext, $context);
    }

    private function createSerializer(): Serializer
    {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());

        // MetadataAwareNameConverter respects #[SerializedName] attributes
        // and falls back to CamelCaseToSnakeCaseNameConverter for other properties
        $nameConverter = new MetadataAwareNameConverter(
            $classMetadataFactory,
            new CamelCaseToSnakeCaseNameConverter()
        );

        $defaultContext = [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
        ];

        $normalizers = [
            new UnixTimestampDateTimeNormalizer(),
            new ObjectNormalizer(
                classMetadataFactory: $classMetadataFactory,
                nameConverter: $nameConverter,
                propertyTypeExtractor: new ReflectionExtractor(),
                defaultContext: $defaultContext,
            ),
            new ArrayDenormalizer(),
        ];

        return new Serializer($normalizers, [new JsonEncoder()]);
    }
}

/**
 * Custom normalizer that handles Unix timestamps for DateTime fields.
 * Normalizes DateTime to Unix timestamp integers for API compatibility.
 * Denormalizes Unix timestamps (integers or numeric strings) to DateTime objects.
 */
class UnixTimestampDateTimeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): ?\DateTimeInterface
    {
        if (null === $data || '' === $data) {
            return null;
        }

        if (is_int($data) || (is_string($data) && ctype_digit($data))) {
            return (new \DateTime())->setTimestamp((int) $data);
        }

        // Handle ISO 8601 strings
        if (is_string($data)) {
            try {
                return new \DateTime($data);
            } catch (\Exception) {
                return null;
            }
        }

        return null;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return ($type === \DateTime::class || $type === \DateTimeInterface::class || $type === \DateTimeImmutable::class)
            && ($data === null || is_int($data) || is_string($data));
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): ?int
    {
        if (!$object instanceof \DateTimeInterface) {
            return null;
        }

        return $object->getTimestamp();
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof \DateTimeInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            \DateTime::class => true,
            \DateTimeInterface::class => true,
            \DateTimeImmutable::class => true,
        ];
    }
}
