<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory;

use App\Criticalmass\DataQuery\Annotation\Queryable;
use App\Criticalmass\DataQuery\AnnotationHandler\AnnotationHandlerInterface;
use App\Criticalmass\DataQuery\EntityProperty\EntityProperty;
use App\Criticalmass\DataQuery\Manager\QueryManagerInterface;
use App\Criticalmass\DataQuery\Query\DateQuery;
use App\Criticalmass\DataQuery\Query\MonthQuery;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\DataQuery\Query\YearQuery;
use App\Criticalmass\DataQuery\QueryProperty\QueryProperty;
use App\Entity\Ride;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class QueryFactory implements QueryFactoryInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var string $entityFqcn */
    protected $entityFqcn;

    /** @var AnnotationHandlerInterface $annotationHandler */
    protected $annotationHandler;

    /** @var QueryManagerInterface $queryManager */
    protected $queryManager;

    /** @var ValueAssignerInterface $valueAssignerInterface */
    protected $valueAssigner;

    public function __construct(RegistryInterface $registry, AnnotationHandlerInterface $annotationHandler, QueryManagerInterface $queryManager, ValueAssignerInterface $valueAssigner)
    {
        $this->registry = $registry;
        $this->annotationHandler = $annotationHandler;
        $this->queryManager = $queryManager;
        $this->valueAssigner = $valueAssigner;
    }

    public function setEntityFqcn(string $entityFqcn)
    {
        $this->entityFqcn = $entityFqcn;

        return $this;
    }

    public function createFromRequest(Request $request): array
    {
        $queryList = [];

        /** @var QueryInterface $query */
        foreach ($this->queryManager->getQueryList() as $queryCandidate) {
            $queryUnderTest = $this->checkForQuery(get_class($queryCandidate), $request);

            if ($queryUnderTest) {
                $queryList[] = $queryUnderTest;
            }
        }

        $dateQuery = $this->checkForQuery(DateQuery::class, $request);

        if ($dateQuery) {
            $queryList[] = $dateQuery;
        } else {
            $monthQuery = $this->checkForQuery(MonthQuery::class, $request);

            if ($monthQuery) {
                $queryList[] = $monthQuery;
            } else {
                $yearQuery = $this->checkForQuery(YearQuery::class, $request);

                if ($yearQuery) {
                    $queryList[] = $yearQuery;
                }
            }
        }

        return $queryList;
    }

    protected function checkForQuery(string $queryFqcn, Request $request): ?QueryInterface
    {
        $requiredQueriableMethodList = $this->annotationHandler->listQueryRequiredMethods($queryFqcn);

        $requiredPropertiesFound = true;

        /** @var QueryProperty $requiredQuerieableMethod */
        foreach ($requiredQueriableMethodList as $requiredQuerieableMethod) {
            if (!$request->query->has($requiredQuerieableMethod->getParameterName())) {
                $requiredPropertiesFound = false;

                break;
            }
        }

        if ($requiredPropertiesFound) {
            $requiredEntityPropertyList = $this->annotationHandler->listRequiredEntityProperties($queryFqcn);

            /** @var EntityProperty $requiredEntityProperty */
            foreach ($requiredEntityPropertyList as $requiredEntityProperty) {
                if (!$this->annotationHandler->hasEntityTypedPropertyOrMethodWithAnnotation(Ride::class, Queryable::class, $requiredEntityProperty->getPropertyName(), $requiredEntityProperty->getPropertyType())) {
                    return null;
                }
            }

            $query = new $queryFqcn();

            /** @var QueryProperty $queryProperty */
            foreach ($requiredQueriableMethodList as $queryProperty) {
                $this->valueAssigner->assignPropertyValue($request, $query, $queryProperty);
            }

            return $query;
        }

        return null;
    }
}
