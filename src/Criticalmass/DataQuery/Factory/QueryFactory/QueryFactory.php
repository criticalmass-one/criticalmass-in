<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\QueryFactory;

use App\Criticalmass\DataQuery\Annotation\Queryable;
use App\Criticalmass\DataQuery\AnnotationHandler\AnnotationHandlerInterface;
use App\Criticalmass\DataQuery\Exception\ValidationException;
use App\Criticalmass\DataQuery\Factory\ConflictResolver\ConflictResolver;
use App\Criticalmass\DataQuery\Factory\ValueAssigner\ValueAssignerInterface;
use App\Criticalmass\DataQuery\Manager\QueryManagerInterface;
use App\Criticalmass\DataQuery\Property\EntityBooleanValueProperty;
use App\Criticalmass\DataQuery\Property\EntityProperty;
use App\Criticalmass\DataQuery\Property\QueryProperty;
use App\Criticalmass\DataQuery\Query\BooleanQuery;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\Util\ClassUtil;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    /** @var ValidatorInterface $validator */
    protected $validator;

    public function __construct(RegistryInterface $registry, AnnotationHandlerInterface $annotationHandler, QueryManagerInterface $queryManager, ValueAssignerInterface $valueAssigner, ValidatorInterface $validator)
    {
        $this->registry = $registry;
        $this->annotationHandler = $annotationHandler;
        $this->queryManager = $queryManager;
        $this->valueAssigner = $valueAssigner;
        $this->validator = $validator;
    }

    public function setEntityFqcn(string $entityFqcn): QueryFactoryInterface
    {
        $this->entityFqcn = $entityFqcn;

        return $this;
    }

    public function createFromRequest(Request $request): array
    {
        $queryList = $this->findEntityDefaultValuesAsQuery();

        /** @var QueryInterface $query */
        foreach ($this->queryManager->getQueryList() as $queryCandidate) {
            $queryUnderTest = $this->checkForQuery(get_class($queryCandidate), $request);

            if ($queryUnderTest) {
                /** @var ConstraintViolationListInterface $constraintViolationList */
                $constraintViolationList = $this->validator->validate($queryUnderTest);

                if ($constraintViolationList->count() === 0) {
                    $key = ClassUtil::getShortname($queryUnderTest);
                    $queryList[$key] = $queryUnderTest;
                } else {
                    $firstMessage = $constraintViolationList->get(0);
                    throw new ValidationException($firstMessage->getMessage());
                }
            }
        }

        $queryList = ConflictResolver::resolveConflicts($queryList);

        return $queryList;
    }

    protected function checkForQuery(string $queryFqcn, Request $request): ?QueryInterface
    {
        $requiredQueriableMethodList = $this->annotationHandler->listQueryRequiredMethods($queryFqcn);

        /** @var QueryProperty $requiredQuerieableMethod */
        foreach ($requiredQueriableMethodList as $requiredQuerieableMethod) {
            if (!$request->query->has($requiredQuerieableMethod->getParameterName())) {
                return null;
            }
        }

        $requiredEntityPropertyList = $this->annotationHandler->listRequiredEntityProperties($queryFqcn);

        if (0 === count($requiredEntityPropertyList)) {
            return null;
        }

        /** @var EntityProperty $requiredEntityProperty */
        foreach ($requiredEntityPropertyList as $requiredEntityProperty) {
            if (!$this->annotationHandler->hasEntityTypedPropertyOrMethodWithAnnotation($this->entityFqcn, Queryable::class, $requiredEntityProperty->getPropertyName(), $requiredEntityProperty->getPropertyType())) {
                return null;
            }
        }

        $query = new $queryFqcn();

        /** @var QueryProperty $queryProperty */
        foreach ($requiredQueriableMethodList as $queryProperty) {
            $this->valueAssigner->assignQueryPropertyValue($request, $query, $queryProperty);
        }

        return $query;
    }

    protected function findEntityDefaultValuesAsQuery(): array
    {
        $defaultValueQueryList = [];
        $entityDefaultValueList = $this->annotationHandler->listEntityDefaultValues($this->entityFqcn);

        /** @var EntityBooleanValueProperty $entityDefaultValue */
        foreach ($entityDefaultValueList as $entityDefaultValue) {
            $booleanQuery = new BooleanQuery();
            $booleanQuery
                ->setPropertyName($entityDefaultValue->getPropertyName())
                ->setValue($entityDefaultValue->getValue());

            $defaultValueQueryList[] = $booleanQuery;
        }

        return $defaultValueQueryList;
    }
}
