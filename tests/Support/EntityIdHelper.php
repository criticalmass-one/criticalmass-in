<?php declare(strict_types=1);

namespace Tests\Support;

/**
 * Most entities have no setId(); tests that need a stable identifier (voters,
 * duplicate detection, grouping) assign it through reflection instead.
 */
final class EntityIdHelper
{
    public static function setId(object $entity, int $id): void
    {
        $reflection = new \ReflectionObject($entity);

        while (!$reflection->hasProperty('id')) {
            $reflection = $reflection->getParentClass();

            if (false === $reflection) {
                throw new \LogicException(sprintf('%s has no id property', get_class($entity)));
            }
        }

        $property = $reflection->getProperty('id');
        $property->setValue($entity, $id);
    }
}
