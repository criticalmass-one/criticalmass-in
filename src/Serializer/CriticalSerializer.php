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

    private function buildContext(array $context): array
    {
        $defaultContext = [
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => fn (object $object): ?int => method_exists($object, 'getId') ? $object->getId() : null,
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
            new DateTimeNormalizer([
                DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:sP',
            ]),
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
