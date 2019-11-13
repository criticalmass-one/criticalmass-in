<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory;

use App\Criticalmass\DataQuery\Annotation\Queryable;
use App\Criticalmass\DataQuery\AnnotationHandler\AnnotationHandlerInterface;
use App\Criticalmass\DataQuery\EntityProperty\EntityProperty;
use App\Criticalmass\DataQuery\Query\BoundingBoxQuery;
use App\Criticalmass\DataQuery\Query\CityQuery;
use App\Criticalmass\DataQuery\Query\DateQuery;
use App\Criticalmass\DataQuery\Query\MonthQuery;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\DataQuery\Query\RadiusQuery;
use App\Criticalmass\DataQuery\Query\RegionQuery;
use App\Criticalmass\DataQuery\Query\YearQuery;
use App\Criticalmass\DataQuery\QueryProperty\QueryProperty;
use App\Entity\CitySlug;
use App\Entity\Region;
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

    public function __construct(RegistryInterface $registry, AnnotationHandlerInterface $annotationHandler)
    {
        $this->registry = $registry;
        $this->annotationHandler = $annotationHandler;
    }

    public function setEntityFqcn(string $entityFqcn)
    {
        $this->entityFqcn = $entityFqcn;

        return $this;
    }

    public function createFromRequest(Request $request): array
    {
        $queryList = [];

        $bbQuery = $this->checkForQuery(BoundingBoxQuery::class, $request);

        if ($bbQuery) {
            $queryList[] = $bbQuery;
        }

        $radiusQuery = $this->checkForQuery(RadiusQuery::class, $request);

        if ($radiusQuery) {
            $queryList[] = $radiusQuery;
        }

        if ($request->query->get('year') && $request->query->get('month') && $request->query->get('day')) {
            $propertyName = 'simpleDate';
            $propertyType = 'string';

            if ($this->annotationHandler->hasEntityTypedPropertyOrMethodWithAnnotation(Ride::class, Queryable::class, $propertyName, $propertyType)) {
                $year = (int)$request->query->get('year');
                $month = (int)$request->query->get('month');
                $day = (int)$request->query->get('day');

                $queryList[] = new DateQuery($year, $month, $day);
            }
        } elseif ($request->query->get('year') && $request->query->get('month')) {
            $propertyName = 'simpleDate';
            $propertyType = 'string';

            if ($this->annotationHandler->hasEntityTypedPropertyOrMethodWithAnnotation(Ride::class, Queryable::class, $propertyName, $propertyType)) {
                $year = (int)$request->query->get('year');
                $month = (int)$request->query->get('month');

                $queryList[] = new MonthQuery($year, $month);
            }
        } elseif ($request->query->get('year')) {
            $propertyName = 'simpleDate';
            $propertyType = 'string';

            if ($this->annotationHandler->hasEntityTypedPropertyOrMethodWithAnnotation(Ride::class, Queryable::class, $propertyName, $propertyType)) {
                $year = (int)$request->query->get('year');

                $queryList[] = new YearQuery($year);
            }
        }

        if ($request->query->get('region')) {
            $region = $this->registry->getRepository(Region::class)->findOneBySlug($request->query->get('region'));

            $queryList[] = new RegionQuery($region);
        }

        if ($request->query->get('citySlug')) {
            /** @var CitySlug $citySlug */
            $citySlug = $this->registry->getRepository(CitySlug::class)->findOneBySlug($request->query->get('citySlug'));

            $queryList[] = new CityQuery($citySlug->getCity());
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
                ValueAssigner::assignPropertyValue($request, $query, $queryProperty);
            }

            return $query;
        }

        return null;
    }
}
