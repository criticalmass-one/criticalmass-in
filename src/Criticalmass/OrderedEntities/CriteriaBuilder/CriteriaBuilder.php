<?php declare(strict_types=1);

namespace App\Criticalmass\OrderedEntities\CriteriaBuilder;

use App\Criticalmass\OrderedEntities\Annotation\AbstractAnnotation;
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
        $criteria->where($expr->lt('dateTime', $orderedEntity->getDateTime()))
            ->andWhere($expr->eq('city', $orderedEntity->getCity()));

        $criteria->orderBy(['dateTime' => 'asc']);

        $this->handleAnnotations($orderedEntity);

        return $criteria;
    }

    protected function handleAnnotations(OrderedEntityInterface $orderedEntity): void
    {
        $reflectionClass = new \ReflectionClass($orderedEntity);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $key => $property) {
            $annotations = $this->annotationReader->getPropertyAnnotations($property);
            
            /** @var AbstractAnnotation $parameterAnnotation */
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Order) {
                    dump($annotation);die;
                }
            }
        }

        return;
    }

}