<?php declare(strict_types=1);

namespace App\Serializer\JMSSerializer\Handler;

use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class RelationHandler
{
    public function __construct(protected ManagerRegistry $doctrine)
    {
    }

    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context): int
    {
        if ($relation instanceof \Traversable) {
            $relation = iterator_to_array($relation);
        }

        if (is_array($relation)) {
            return array_map($this->getSingleEntityRelation(...), $relation);
        }

        return $this->getSingleEntityRelation($relation);
    }

    protected function getSingleEntityRelation($relation): int
    {
        $metadata = $this->doctrine->getManager()->getClassMetadata($relation::class);

        $ids = $metadata->getIdentifierValues($relation);
        if (!$metadata->isIdentifierComposite) {
            $ids = array_shift($ids);
        }

        return $ids;
    }

    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context)
    {
        $className = $type['params'][0]['name'] ?? null;

        if (!class_exists($className, true)) {
            throw new \InvalidArgumentException('Class name should be explicitly set for deserialization');
        }

        $metadata = $this->doctrine->getManager()->getClassMetadata($className);

        if (!is_array($relation)) {
            return $this->doctrine->getManager()->getReference($className, $relation);
        }

        $single = false;
        if ($metadata->isIdentifierComposite) {
            $single = true;
            foreach ($metadata->getIdentifierFieldNames() as $idName) {
                $single = $single && array_key_exists($idName, $relation);
            }
        }

        if ($single) {
            return $this->doctrine->getManager()->getReference($className, $relation);
        }

        $objects = [];
        foreach ($relation as $idSet) {
            $objects[] = $this->doctrine->getManager()->getReference($className, $idSet);
        }

        return $objects;
    }
}
