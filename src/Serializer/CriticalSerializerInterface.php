<?php declare(strict_types=1);

namespace App\Serializer;

use \Symfony\Component\Serializer\SerializerInterface;

interface CriticalSerializerInterface extends SerializerInterface
{
    final public const string FORMAT = 'json';

    public function serialize(mixed $data, string $format = self::FORMAT, array $context = []): string;
    public function deserialize(mixed $data, string $type, string $format = self::FORMAT, array $context = []): mixed;
    /** @param array<string, mixed> $context */
    public function deserializeInto(mixed $data, object $target, string $format = self::FORMAT, array $context = []): object;
}
