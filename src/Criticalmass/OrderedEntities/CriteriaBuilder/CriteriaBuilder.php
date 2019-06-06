<?php declare(strict_types=1);

namespace App\Criticalmass\OrderedEntities\CriteriaBuilder;

use App\Criticalmass\OrderedEntities\Annotation\AbstractAnnotation;
use App\Criticalmass\OrderedEntities\Annotation\Identical;
use App\Criticalmass\OrderedEntities\Annotation\Order;
use App\Criticalmass\OrderedEntities\OrderedEntityInterface;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Collections\Criteria;

class CriteriaBuilder implements CriteriaBuilderInterface
{
    /** @var Reader $annotationReader */
    protected $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function build(OrderedEntityInterface $orderedEntity, string $direction): Criteria
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();

        $criteria->where($expr->lt('dateTime', $orderedEntity->getDateTime()));

        $criteria = $this->handleAnnotations($orderedEntity, $criteria);

        return $criteria;
    }

    protected function handleAnnotations(OrderedEntityInterface $orderedEntity, Criteria $criteria): Criteria
    {
        $reflectionClass = new \ReflectionClass($orderedEntity);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $key => $property) {
            $annotations = $this->annotationReader->getPropertyAnnotations($property);

            /** @var AbstractAnnotation $parameterAnnotation */
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Order) {

                    $criteria->orderBy([$property->getName() => $annotation->getDirection()]);
                }

                if ($annotation instanceof Identical) {
                    $getMethodName = sprintf('get%s', ucfirst($property->getName()));

                    $criteria->andWhere(Criteria::expr()->eq($property->getName(), $orderedEntity->$getMethodName()));
                }
            }
        }

        return $criteria;
    }
}
